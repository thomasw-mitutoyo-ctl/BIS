import logging
import threading

import time

FAILURE_RETRY_TIME = 10
log = logging.getLogger(__name__)


class WeatherSource:
    """
    The WeatherSource class is the base class for all weather sources. A weather source may be an online service or
    a weather station.
    """

    def __init__(self):
        pass

    def run(self):
        raise NotImplemented("method stub needs to be implemented!")

    def start(self):
        """
        Starts the WeatherSource in a new thread. If the code crashes it will be restarted after a specified amount
        of time
        """
        log.info("Starting WeatherSource")
        thread = WeatherSourceThread(self)
        thread.start()


class WeatherSourceThread(threading.Thread):
    """
    This is the wrapper thread for a WeatherSource
    """

    def __init__(self, weather_source):
        super(WeatherSourceThread, self).__init__()
        self.weather_source = weather_source

    def run(self):

        # Run forever
        while True:
            try:
                self.weather_source.run()
                log.warn("WeatherSource stopped without exception, do not restart")
                break
            except NotImplemented as e:
                log.warn("Method not implemented: " + str(e))
            except Exception as e:
                log.exception("WeatherSource threw an exception: " + str(e))

            # When an exception happened, sleep and restart
            log.info("Restarting WeatherSource in %s seconds", FAILURE_RETRY_TIME)
            time.sleep(FAILURE_RETRY_TIME)
