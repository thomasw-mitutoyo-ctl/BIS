import json
import logging
import threading
from BaseHTTPServer import BaseHTTPRequestHandler, HTTPServer

log = logging.getLogger(__name__)


class RestWeatherProvider(threading.Thread):
    """
    The  RestWeatherProvider serves the collected weather data using a simple http server. The weather data can be
    obtained by doing a simple http GET request
    """

    def __init__(self, repository, address, port):
        super(RestWeatherProvider, self).__init__()
        self.repository = repository
        self.port = port
        self.address = address

    def run(self):
        try:
            log.info("Starting WeatherProvider")

            # Create and start the http server
            server = HTTPServer((self.address, self.port), self.request_handler)
            server.serve_forever()
        except Exception as e:
            log.exception("WeatherProvider threw an exception: " + str(e))

    def request_handler(self, *args):
        HTTPRequestHandler(self.repository, *args)


class HTTPRequestHandler(BaseHTTPRequestHandler):
    """
    HTTPRequestHandler for the RestWeatherProvider
    """

    def __init__(self, repository, *args):
        self.repository = repository
        BaseHTTPRequestHandler.__init__(self, *args)

    # noinspection PyPep8Naming
    def do_GET(self):
        """
        Handles the GET request and returns the weather in json format
        """

        self.send_response(200)
        self.send_header('Content-type', 'application/json;charset=utf-8')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.end_headers()
        data = self.repository.get_all_data()
        self.wfile.write(str(json.dumps(data)))
