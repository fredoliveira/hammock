## Example messages to post:

### New case:

    {
      "type": "new_case",
      "user": "{{case.customer.name}}", 
      "id": "{{case.id}}",
      "url": "{{case.direct_url}}",
      "subject": "{{case.subject}}"
    }

### New comment:

    {% for email in case.emails %}
      {% if forloop.rindex0 == 0 %}
        {
          "type": "case_update",
          "from": "{{email.from}}",
          "body": "{{email.body}}",
          "id": "{{case.id}}",
          "url": "{{case.direct_url}}",
          "subject": "{{case.subject}}"
        }
      {% endif %}
    {% endfor %}
