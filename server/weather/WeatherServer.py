#!/usr/bin/python

import logging.handlers
import json

from OpenWeatherMapWeatherSource import OpenWeatherMapWeatherSource
from RestWeatherProvider import RestWeatherProvider
from RestWeatherSource import RestWeatherSource
from WeatherDataRepository import WeatherDataRepository

SERVER_ADDRESS = "localhost"
SERVER_PORT = 9999
SERVER_PUSH_PORT = 8888
OPEN_WEATHER_MAP_TOKEN = 'please configure the token in config.json'
OPEN_WEATHER_MAP_POLL_INTERVAL = 600
CITY_LIST = {
    "Oberndorf": "2859654"
}


if __name__ == "__main__":
    """
    Main Weatherdaemon server entry point
    """

    # Configure logging
    # noinspection SpellCheckingInspection
    logging.basicConfig(format='[%(levelname)s][%(asctime)s] %(module)s.%(funcName)s: %(message)s',
                        datefmt='%m/%d/%Y %I:%M:%S %p',
                        level=0)
    log = logging.getLogger(__name__)

    data = None

    # Try reading the configuration file
    try:
        with open('config.json') as json_data_file:
            data = json.load(json_data_file)
    except Exception as e:
        log.exception("Failed to read configuration file: " + str(e))

    # When the file existed and was read, parse the values and configure the weatherdaemon
    if data is not None:
        try:
            SERVER_ADDRESS = data['Hostname']
            SERVER_PORT = data['ServerPort']
            SERVER_PUSH_PORT = data['PushServerPort']
            OPEN_WEATHER_MAP_TOKEN = data['OpenWeatherMapToken']
            OPEN_WEATHER_MAP_POLL_INTERVAL = data['OpenWeatherMapPollInterval']
            CITY_LIST = {}

            for city_name in data['OpenWeatherMapCities']:
                CITY_LIST[city_name] = data['OpenWeatherMapCities'][city_name]

        except Exception as e:
            log.exception("Failed to read config file: " + str(e))
            exit(-1)

    # Create the repository which will contain the collected data
    repository = WeatherDataRepository()
    repository.set_preferred_source("Weather", "RestWeatherSource")

    # Create and start the RestWeatherSource
    restWeatherSource = RestWeatherSource(repository, SERVER_ADDRESS, SERVER_PUSH_PORT)
    restWeatherSource.start()

    # Create and start the OpenWeatherMapWeatherSource
    pollWeatherSource = OpenWeatherMapWeatherSource(OPEN_WEATHER_MAP_TOKEN,
                                                    CITY_LIST, OPEN_WEATHER_MAP_POLL_INTERVAL, repository)
    pollWeatherSource.start()

    # Create and start the RestWeatherProvider, it serves the weather data on port 9999
    restWeatherProvider = RestWeatherProvider(repository, SERVER_ADDRESS, SERVER_PORT)
    restWeatherProvider.start()
