## TI Extra Vars Extension

Adds extra variables to Menu/Category/Location items that can be edited in Menu Admin, and queried from a template file. 

## Installation

Copy this into your extension folder as `/extensions/cupnoodles/extravars`. 

## Usage

Create new fields at `admin/cupnoodles/extravars/extravars` (nav link is under 'Design').

Once you do, you'll be able to edit those fields on either the Menu or Location admin page, whichever the new field it set for. New fields will be at the bottom of the page. `name` is the value that shows up on the admin editing page. `slug` is what you'll reference to get the extra variable in your template.

**MAJOR CAVEAT** Don't set the name of your slug to anything that TI's menu item object could already have set (like 'menu_options'). Odd things will happen and your template will almost definitely break. 


To actually use the new value, call the new `->getExtraVarValue()` function on your model from within a template partial. 

For example, to create a new 'subtitle' element on a menu item, set 'slug' to 'my_new_menu_subtitle', save a string value into the menu item as the new subtitle, and then in `<your_template>/_partials/menu/item.blade.php` display the new value with `{{ $menuItem->getExtraVarValue('my_new_menu_subtitle') }}`.

Usage Note: all values are saved as a string with max length 128 and have absolutely no sanitization done to them at all. This means it's easy to break your own template if you inject malformed html into a `string` type field and display it, but it's kept this way for the added flexibility it can add. You can, for instance, add extra images to a menu item by saving upload urls, or even create new CSS classnames to apply to individual menu items or locations, and then edit those within the admin menu. 