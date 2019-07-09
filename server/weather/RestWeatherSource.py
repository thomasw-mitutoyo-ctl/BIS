import logging
from BaseHTTPServer import BaseHTTPRequestHandler, HTTPServer
from WeatherSource import WeatherSource

log = logging.getLogger(__name__)


class RestWeatherSource(WeatherSource):
    """
    This WeatherSource serves a simple HTTP server which accepts simple GET requests. When a GET request arrived
    the repository gets updated
    """
    def __init__(self, repository, address, port):
        WeatherSource.__init__(self)
        self.port = port
        self.address = address
        self.repository = repository

    def run(self):
        server = HTTPServer((self.address, self.port), self.request_handler)
        server.serve_forever()

    def request_handler(self, *args):
        HTTPRequestHandler(self.repository, *args)


class HTTPRequestHandler(BaseHTTPRequestHandler):
    """
    Request handler for the RestWeatherSource
    """

    def __init__(self, repository, *args):
        self.repository = repository
        BaseHTTPRequestHandler.__init__(self, *args)

    # noinspection PyPep8Naming
    def do_GET(self):
        """
        Processes a GET request
        :return:
        """

        city = self.headers.get('city')

        # Extract the weather data from the request header
        weather_data = {'latitude': self.headers.get('latitude'),
                        'longitude': self.headers.get('longitude'),
                        'barometer': self.headers.get('barometer'),
                        'temperature': self.headers.get('temperature'),
                        'temperature_min': self.headers.get('temperature_min'),
                        'temperature_max': self.headers.get('temperature_max'),
                        'humidity': self.headers.get('humidity'),
                        'precipitation': self.headers.get('precipitation'),
                        'wind_speed': self.headers.get('wind_speed'),
                        'sunrise': self.headers.get('sunrise'),
                        'sunset': self.headers.get('sunset'),
                        'icon': '0'}

        weather_data = dict((k, v) for k, v in weather_data.iteritems() if v is not None)

        if city is not None and len(weather_data) > 0:
            log.debug("Got new weather data for " + city + ": " + str(weather_data))
            self.repository.put_data_for_city("RestWeatherSource", city, weather_data)

            self.send_response(200)
        else:
            self.send_response(400)
