from Logger import Logger
logger = Logger.logger
from Output import Output
from Stats import Stats
import Config
from Config import Conf, Plugin, Aliases, CommandLineOptions

import re, time

class Monitor:

    def __init__(self, plugin, watch_rule):
        self.plugin = plugin
        self.options = CommandLineOptions().get_options()

        # read configuration
        self._conf = Conf()
        if self.options.config_file:
            conffile = self.options.config_file
        else:
            conffile = self._conf.DEFAULT_CONFIG_FILE
        self._conf.read([conffile])
        self.watch_rule = watch_rule

        self.queries = \
            self.get_replaced_values('query', self.watch_rule.dict())
        self.regexps = \
            self.get_replaced_values('regexp', self.watch_rule.dict())
        self.results = \
            self.get_replaced_values('result', self.watch_rule.dict())

        self.initial_time = int(time.time()) # initial time at object call
        self.first_value = None

        self.open()

    def get_replaced_values(self, key, groups):

        # replace plugin variables with watch_rule data
        #
        # for example, given the following watch_rule:
        # 
        #     watch-rule plugin_id="2006" plugin_sid="1" condition="eq"
        #                value="1" from="192.168.6.64" to="192.168.6.63"
        #                port_from="5643" port_to="22"
        #
        #  and the following plugin query:
        #     query = {$from}:{$port_from} {$to}:{$port_to}
        #
        #  replace the variables with the watch-rule data:
        #     query = 192.168.6.64:5643 192.168.6.63:22

        values = {}
        for rule_name, rule in self.plugin.rules().iteritems():
            values[rule_name] = \
                self.plugin.get_replace_value(rule[key],
                                              groups)

        return values

    def _plugin_defaults(self, event, log):


        # get default values from config
        #
        if self._conf.has_section("plugin-defaults"):

        # 1) date
            default_date_format = self._conf.get("plugin-defaults",
                                                 "date_format")
            if event["date"] is None and default_date_format:
                event["date"] = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time()))
                event["fdate"] = time.strftime('%Y-%m-%d %H:%M:%S', time.localtime(time.time()))



        # 2) sensor
            default_sensor = self._conf.get("plugin-defaults", "sensor")
            if event["sensor"] is None and default_sensor:
                event["sensor"] = default_sensor

        # 3) interface
            default_iface = self._conf.get("plugin-defaults", "interface")
            if event["interface"] is None and default_iface:
                event["interface"] = default_iface

        # 4) source ip
            if event["src_ip"] is None:
                event["src_ip"] = event["from"]

        # 5) dest ip
            if event["dst_ip"] is None:
                event["dst_ip"] = event["to"]

        # 6) protocol
            if event["protocol"] is None:
                event["protocol"] = "TCP"

        # 7) ports
            if event["src_port"] is None:
                event["src_port"] = event["port_from"]
            if event["dst_port"] is None:
                event["dst_port"] = event["port_to"]
            if event["src_port"] is None:
                event["src_port"] = 0
            if event["dst_port"] is None:
                event["dst_port"] = 0
            if event["src_ip"] is None:
                event["src_ip"] = event["sensor"]
            if event["dst_ip"] is None:
                event["dst_ip"] = event["sensor"]
	    event["log"] = log





        # the type of this event should always be 'monitor'
        if event["type"] is None:
            event["type"] = 'monitor'

        # Clean up mess
        event["port_from"] = ""
        event["port_to"] = ""
        event["to"] = ""
        event["from"] = ""
        event["absolute"] = ""
        event["interval"] = ""

        return event


    # given the server's watch_rule, find what rule to apply
    def match_rule(self):
        plugin_sid = self.watch_rule['plugin_sid']
        for rule_name, rule in self.plugin.rules().iteritems():
            for sid in Config.split_sids(str(rule['sid'])): # sid=1,2-4,5
                if str(plugin_sid) == str(sid) or str(sid).lower() == 'any':
                    return rule_name
        return None

    # eval watch rule condition
    def eval_condition(self, cond, arg1, arg2, value):

        if type(arg1) is not int:
            try:
                arg1 = int(arg1)
            except ValueError:
                logger.warning(
                    "value returned by monitor (arg1=%s) is not an integer" % \
                    str(arg1))
                return False

        if type(arg2) is not int:
            try:
                arg2 = int(arg2)
            except ValueError:
                logger.warning(
                    "value returned by monitor (arg2=%s) is not an integer" % \
                    str(arg2))
                return False

        if type(value) is not int:
            try:
                value = int(value)
            except ValueError:
                logger.warning(
                    "value returned by monitor (value=%s) is not an integer" % \
                    str(value))
                return False

        logger.debug("Monitor expresion evaluation: " +\
            "%s(arg2) <%s> %s(arg1) + %s(value)?" %\
            (str(arg2), str(cond), str(arg1), str(value)))

        if cond == "eq":
            return (int(arg2) == int(arg1) + int(value))
        elif cond == "ne":
            return (int(arg2) != int(arg1) + int(value))
        elif cond == "gt":
            return (int(arg2) > int(arg1) + int(value))
        elif cond == "ge":
            return (int(arg2) >= int(arg1) + int(value))
        elif cond == "le":
            return (int(arg2) <= int(arg1) + int(value))
        elif cond == "lt":
            return (int(arg2) < int(arg1) + int(value))
        else:
            return False
 
    # given the watch rule, ask to Monitor and obtain a result
    # *must* be overriden in child classes:
    # different implementations for each type of monitor
    # (socket, database, etc.)
    def get_data(self, rule_name):
        pass

    # *must* be overriden in child classes:
    def open(self):
        pass

    # *must* be overriden in child classes:
    def close(self):
        pass


    # TODO: merge with ParserLog.feed()
    #
    def get_value(self, monitor_response, rule_name):

        value = None
        hash = {}
        count = 1

        regexp = self.regexps[rule_name]
        pattern = re.compile(regexp, re.IGNORECASE|re.MULTILINE)
        
        match = pattern.search(monitor_response)

        if match is not None:
            groups = match.groups()

            for group in groups:

                # group by index ()
                if group is None: group = ''
                hash.update({str(count): str(group)})
                count += 1

                # group by name (?P<name-of-group>)
                hash.update(match.groupdict())
	else:
		return None


        # first, try getting substitution from the regular expresion syntax
        result = self.results[rule_name]
        value = self.plugin.get_replace_value(result, hash)
        try:
            val = int(value.split(".")[0])
        except:
            return False

        return val


    # get a new value from monitor and compare with the first one
    # returns True if the condition apply, False in the other case
    def evaluate(self, rule_name):
        
        if self.first_value is None:
            logger.debug("Can not extract value (arg1) from monitor response or no initial value to compare with")
            return True

        value = None
        monitor_response = self.get_data(rule_name)
        if not monitor_response:
            logger.warning("No data received from monitor")
            return True
        else:
            value = self.get_value(monitor_response, rule_name)
            if value is None:
	    	return True
	    #if not value:
            #    continue
            if self.eval_condition(cond = self.watch_rule["condition"],
                                   arg1 = self.first_value,
                                   arg2 = value,
                                   value = int(self.watch_rule["value"])):
                self.watch_rule["type"] = "monitor"
		try:
			cond = self.watch_rule["condition"]
                        arg1 = self.first_value
                        arg2 = value
                        value = int(self.watch_rule["value"])
                        comm = self.queries
			log = "Monitor Command: %s , Monitor expresion evaluation: %s(arg2) <%s> %s(arg1) + %s(value)? , Command Response: %s" % (str(comm), str(arg2), str(cond), str(arg1), str(value), monitor_response.replace("\n", "\r"))
		except:
			log = "Monitor Exception"
                self.watch_rule = self._plugin_defaults(self.watch_rule, log)
                Output.event(self.watch_rule)
                Stats.new_event(self.watch_rule)
                return True

        logger.debug("No data matching the watch-rule received from monitor")
        return False


    # *may* be overriden in child classes
    def process(self):

        # get the name of rule to apply
        rule_name = self.match_rule()
        if rule_name is not None:
            logger.info("Matched rule: [%s]" % (rule_name))

        # get data from plugin (first time)
            if self.first_value is None:

        # <absolute> is "no" by default
        # the absence of <interval> implies that <absolute> is "yes"
                if self.watch_rule['absolute'] in ('yes', 'true') or\
                   not self.watch_rule['interval']:
                    self.first_value = 0
                else:
                    monitor_response = self.get_data(rule_name)
                    if not monitor_response:
                        self.first_value = 0
                    for resp in monitor_response:
                        if resp:
                            self.first_value = self.get_value(resp, rule_name)
                            if self.first_value == False:
                                self.first_value = 0

        # get current time
        current_time = int(time.time())

        # Three posibilities:
        #
        # 1) no interval specified, no need to wait
        if not self.watch_rule.dict().has_key('interval'):
            self.evaluate(rule_name)
            return True

        # 1) no interval specified, no need to wait
        elif not self.watch_rule['interval']:
            self.evaluate(rule_name)
            return True

        # 2) we are in time, check the result of the watch-rule
        elif (self.initial_time + \
                int(self.watch_rule["interval"]) >current_time):
            return self.evaluate(rule_name)

        # 3) we are out of time
        else:
            self.evaluate(rule_name)
            return True


class MonitorFile(Monitor):
    pass



# vim:ts=4 sts=4 tw=79 expandtab: