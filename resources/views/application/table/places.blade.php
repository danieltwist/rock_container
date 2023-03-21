{{ $application->send_from_country }}, {{ is_null($application->send_from_city) ?: implode('/', $application->send_from_city) }} -
{{ $application->send_to_country }}, {{ is_null($application->send_to_city) ?: implode('/', $application->send_to_city) }}
<br>
Депо сдачи: {{ $application->place_of_delivery_country }}, {{ is_null($application->place_of_delivery_city) ?: implode('/', $application->place_of_delivery_city) }}
