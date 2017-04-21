# ACF Taxonomy Select Field

ACF plugin for selecting taxonomies (NOT taxonomy terms)

-----------------------

### Description

The built-in Taxonomy field in Advanced Custom Fields only allows you to select taxonomy terms. This field allows you to select an actual taxonomy (and get its slug or object), and _not_ a term.

### Compatibility

This ACF field type is compatible with:
* ACF 5

### Options

#### Choose Allowed taxonomies

Select the taxonomies you want to provide as choices. Leave blank to allow selection of any taxonomy.

#### Return Format

Choose whether you want to receive a taxonomy slug or object.

Example return object:

```
stdClass WP_Taxonomy (
[hierarchical] =>
[update_count_callback] =>
[rewrite] =>
  Array ( [slug] => features [with_front] => 1 )
[query_var] => features
[public] => 1
[show_ui] => 1
[show_tagcloud] => 1
[_builtin] =>
[labels] =>
  stdClass Object (
    [name] => Features
    [singular_name] => Feature
    [search_items] => Search Features
    [popular_items] => Popular Features
    [all_items] => All Features
    [parent_item] => Parent Feature
    [parent_item_colon] => Parent Feature:
    [edit_item] => Edit Feature
    [update_item] => Update Feature
    [add_new_item] => Add New Feature
    [new_item_name] => New Feature Name
    [separate_items_with_commas] => Separate Features with commas
    [add_or_remove_items] => Add or remove Features
    [choose_from_most_used] => Choose from the most used Features
  )
[show_in_nav_menus] => 1
[label] => Features
[singular_label] => Feature
[cap] =>
  stdClass Object (
    [manage_terms] => manage_categories
    [edit_terms] => manage_categories
    [delete_terms] => manage_categories
    [assign_terms] => edit_posts
  )
[name] => features
[object_type] =>
  Array ( [0] => rentals [1] => rentals )
)
```

### Installation

1. Copy the `acf-taxonomy-select` folder into your `wp-content/plugins` folder
2. Activate the acf-taxonomy-select plugin via the plugins admin page
3. Create a new field via ACF and select the Taxonomy Select type
4. Please refer to the description for more info regarding the field type settings

### Changelog
Please see `readme.txt` for changelog
