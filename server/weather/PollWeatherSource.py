import logging
import time

from WeatherSource import WeatherSource

log = logging.getLogger(__name__)


class PollWeatherSource(WeatherSource):
    """
    This class abstracts a WeatherSource which polls the data frequently
    """

    def __init__(self, interval, repository):
        WeatherSource.__init__(self)
        self.stopped = False
        self.interval = interval
        self.repository = repository

    def run(self):

        # Poll the weather data until stopped
        while not self.stopped:
            log.debug("Polling weather data")
            success, weather_data = self.poll_weather_data()

            if success:
                log.debug("Successfully polled weather data: " + str(weather_data))
                for city, data in weather_data.iteritems():
                    self.repository.put_data_for_city("PollWeatherSource", city, data)
            else:
                log.error("Failed to poll weather data")

            time.sleep(self.interval)

    def poll_weather_data(self):
        """
        Method stub. Overwrite to poll the data from wherever needed
        :return: (True|False, Weather data|None)
        """
        raise NotImplemented("method stub needs to be implemented!")
