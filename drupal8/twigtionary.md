# Twigtionary

Welcome to Twigtionary. This is a list of helpful twig snippets to help your D8 project.

## Current path
```twig
{{ path('<current>') }}

{# String in current path? #}
{% if 'string' in path('<current>') %}
  {# do something #}
{% endif %}
```

## Image URI from simple image
```twig
{{ file_url(content.field_image.0.entity.uri.value) }}
```

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

## Use a shared template to pass var into from a node template
Create your shared template. For example, `page-banner.html.twig`.

```twig
<div class="page-banner page-banner--has-bookmark">
  <div class="container">

    {% if content_type | render %}
    <div class="page-banner__content-type">
      <span>{{ content_type }}<span>
    </div>
    {% endif %}

    {% if bookmark | render %}{{ bookmark }}{% endif %}

    <h1 class="page-banner__title">{{ label }}</h1>

    {% if intro | render %}{{ intro }}{% endif %}

    {% if meta | render %}
    <div class="meta">
      {% for item in meta %}
        {{ item }}
      {% endfor %}
    </div> 
    {% endif %}

    {% if image|length <= 2 %}
    <hr>
    {% endif %}
  </div>
</div><!-- page-banner -->
{% if image | render  %}
  <div class="page-banner__image-below">
    {{ image }}
  </div>
{% endif %}
```

Add an include in your node template that references a dictionary of fields you want to pass to the shared header

```twig
{% 
  set header_fields = {
    'content_type': node.bundle | replace({'_':' '}) | upper,
    'bookmark': content.flag_bookmark,
    'page-title': label,
    'meta': [content.field_grade_level],
  }
%}

{% include '@ts_ttol/shared/page-banner-with-bookmark.html.twig' with header_fields %}
```
