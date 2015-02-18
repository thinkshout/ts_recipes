So, variable_set and variable_get are gone. Instead, you get a "config" table that is accessed using some simple functions. First, tell the system what content you want in your table entry by creating:

`config/schema/modulename.schema.yml`


modulename.table_entry:
  type: mapping
  label: 'My Module Settings'
  mapping:
    some_value:
      type: string
      label: 'A string value'
    some_integer:
      type: integer
      label: 'An int value'
    some_bool:
      type: boolean
      label: 'A boolean value'
 
Create defaults by creating a file like so:
`config/install/modulename.table_entry.yml`
`
some_value: 'default string here'
some_int: 42
some_bool: FALSE
`

Now, the equivalents of variable_get and variable_set work in two ways. If you are going to do multiple calls in the same function, assign the config object to a variable:

`$config = $this->config('modulename.table_entry');`
`$val = $config->get('some_integer');`
`$config->set('some_integer', $val + 3)->save();`

Otherwise, you can do the long form for one-offs:
`$yes_or_no = $this->config('modulename.table_entry')->get('some_bool');`