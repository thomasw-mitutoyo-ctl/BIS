import datetime


class WeatherDataRepository:
    """
    The WeatherDataRepository contains the complete weather data. This data can be updated and queried at any time.

    Valid weather values are:
    'temperature'
    'temperature_min'
    'temperature_max'
    'barometer'
    'humidity'
    'wind_speed'
    'sunrise'
    'sunset'
    'latitude'
    'longitude'
    'precipitation'
    'icon'

    """

    def __init__(self):
        self.buffer = {}
        self.preferred_source = {}

    def put_data_for_city(self, source, city_name, data):
        """
        Inserts or updates data for a specific city
        :param source:
        :param city_name:
        :param data:
        :return:
        """

        now = datetime.datetime.now()

        if city_name in self.preferred_source:
            # When the city has a preferred source do further tests

            if self.preferred_source[city_name] == source:
                # When the given source is preferred for this city, update the data
                self._update_for_city(city_name, data, now)
                return

            if city_name in self.buffer:
                # When there is existing data for the city, update it when the data is older than x seconds
                d, time = self.buffer[city_name]

                if (now - time).total_seconds() > 60 * 30:
                    self._clear_data_for_city(city_name)
                    self._update_for_city(city_name, data, now)
                    return
            else:
                # When there is no existing data use the given data
                self._update_for_city(city_name, data, now)

        else:
            # When the city has no preferred source, update it with the given data
            self._update_for_city(city_name, data, now)

    def _update_for_city(self, city_name, data, time):
        """
        Updates the weather data for the city using the new data
        :param city_name:
        :param data:
        :param time:
        :return:
        """

        # Either get existing values or create a new dictionary
        if city_name in self.buffer:
            weather_data, t = self.buffer[city_name]
        else:
            weather_data = {'temperature': None,
                            'temperature_min': None,
                            'temperature_max': None,
                            'barometer': None,
                            'humidity': None,
                            'wind_speed': None,
                            'sunrise': None,
                            'sunset': None,
                            'latitude': None,
                            'longitude': None,
                            'precipitation': None,
                            'icon': None}

        # Update the new or existing values
        self._update_changed_values_of_dict(weather_data, data)

        self.buffer[city_name] = weather_data, time

    def _clear_data_for_city(self, city_name):
        if city_name in self.buffer:
            del self.buffer[city_name]

    @staticmethod
    def _update_changed_values_of_dict(target, source):
        """
        Replaces the values in the target dictionary with values in the source dictionary
        """
        for key in target:
            if key in source:
                target[key] = source[key]

    def get_data_for_city(self, city_name):
        """
        Returns weather data for a specific city
        :param city_name:
        :return:
        """
        return self.buffer[city_name]

    def get_all_data(self):
        """
        Returns the weather data of all cities
        :return:
        """
        data = {}

        for city_name in self.buffer:
            d, time = self.buffer[city_name]

            data[city_name] = d

        return data

    def set_preferred_source(self, city, source):
        self.preferred_source[city] = source
