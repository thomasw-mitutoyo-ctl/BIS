import logging

from PollWeatherSource import PollWeatherSource
import requests

log = logging.getLogger(__name__)


class OpenWeatherMapWeatherSource(PollWeatherSource):
    """
    This implementation of the PollWeatherSource reads weather data from the OpenWeatherMap service
    """

    def __init__(self, token, city_list, interval, repository):
        PollWeatherSource.__init__(self, interval, repository)
        self.token = token
        self.city_list = city_list
        self.endpoint = 'http://api.openweathermap.org/data/2.5/group'
        self.units = 'metric'

    def poll_weather_data(self):
        request_string = self.build_request_string()

        try:

            # Do the request using the generated request string
            response = requests.get(request_string)

            if response.status_code == requests.codes.ok:
                # Process the response when successful
                return self.process_response(response.json())
            else:
                log.warn("Failed to read weather data, response status code: " + str(response.status_code))
                return False, {}

        except Exception as e:
            log.exception("Failed to read weather data: " + str(e))
            return False, {}

    def build_request_string(self):
        """
        Builds the request string used to get the weather data from the api
        """

        ids = ""

        # Build the city id list
        for city, city_id in self.city_list.items():
            ids += city_id + ","

        # remove the last comma in the string
        ids = ids[:-1]

        # noinspection SpellCheckingInspection
        return self.endpoint + '?id=' + ids + '&units=' + self.units + '&appid=' + self.token

    def process_response(self, data):
        """
        Interprets the response of the request
        """
        weather_data = {}

        for city_data in data['list']:

            city_name = city_data['name']

            for name, city_id in self.city_list.iteritems():
                if city_id == str(city_data['id']):
                    city_name = name

            weather = {'latitude': str(city_data['coord']['lat']),
                       'longitude': str(city_data['coord']['lon']),
                       'temperature': str(city_data['main']['temp']),
                       'temperature_min': str(city_data['main']['temp_min']),
                       'temperature_max': str(city_data['main']['temp_max']),
                       'humidity': str(city_data['main']['humidity']),
                       'wind_speed': str(city_data['wind']['speed']),
                       'sunrise': str(city_data['sys']['sunrise']),
                       'sunset': str(city_data['sys']['sunset']),
                       'icon': str(city_data['weather'][0]['id'])}

            weather_data[city_name] = weather

        return True, weather_data
