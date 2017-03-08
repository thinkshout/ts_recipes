# Twigtionary

Welcome to Twigtionary. This is a list of helpful twig snippets to help your D8 project.

## Image URI from a media object
```twig
{% for key, image in content.field_image  %}
  {%if image.entity %}
    {% set media = image.entity %}
    {% set file = media.field_image.entity %}
    {% set uri = file_url(file.uri.value) %}
    <img src="{{ uri }}" alt="{{ media.name.value }}" />
  {% endif %}
{% endfor %}
```
