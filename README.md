# Extra Vars for TastyIgniter

Add your own fields to **menu items**, **categories** and **locations**, fill them in on the normal
admin edit screens, and use them in your theme. Good for subtitles, "spicy" badges, per-category
CSS classes, seat counts and other small bits of data TastyIgniter doesn't have a field for.

> Using TastyIgniter v3? Use the [`v3` branch](https://github.com/CupNoodles/ti-ext-extravars/tree/v3).

## Requirements

- TastyIgniter v4
- Cart and Local extensions (installed with TastyIgniter by default)

## Installation

### From GitHub (git clone)

From your TastyIgniter root folder, clone the extension into `extensions/cupnoodles/extravars`. The
folder name matters: TastyIgniter finds extensions at `extensions/<vendor>/<name>`.

```bash
git clone https://github.com/CupNoodles/ti-ext-extravars.git extensions/cupnoodles/extravars
php artisan igniter:up
```

`igniter:up` creates the extension's database tables. To update later:

```bash
cd extensions/cupnoodles/extravars
git pull
cd ../../..
php artisan igniter:up
```

### With Composer, from GitHub

If you manage TastyIgniter with Composer, you can install straight from the GitHub repository
without Packagist or the marketplace:

```bash
composer config repositories.cupnoodles-extravars vcs https://github.com/CupNoodles/ti-ext-extravars
composer require cupnoodles/ti-ext-extravars:dev-master
php artisan igniter:up
```

Once a version is tagged, you can require `^4.0` instead of `dev-master`. Updates then come through
`composer update`.

## Usage

1. Go to **Design > Extra Vars** and click **New**.
2. Give the field a **name** (the label admins see) and choose what to **add it to** (menu items,
   categories or locations) and its **type**:
   - **Text**: any text, returned as a string
   - **Number**: returned as an int or float
   - **On/off**: a switch, returned as `true`/`false`
3. The field appears on the edit screen of every menu item, category or location. On tabbed forms
   it's under an **Extra fields** tab. It's saved with the normal **Save** button.
4. Read the value in your theme using the field's **code**:

```blade
@if ($subtitle = $menuItem->getExtraVarValue('subtitle'))
    <p class="menu-subtitle">{{ $subtitle }}</p>
@endif

@if ($menuItem->getExtraVarValue('spicy'))
    <span class="badge">Spicy</span>
@endif

{{-- With a fallback when no value is saved --}}
{{ $location->getExtraVarValue('seats', 'Ask us') }}

{{-- All extra vars for a record, keyed by code --}}
@foreach ($category->getExtraVars() as $code => $value) ... @endforeach
```

Values are loaded for a whole list at once, so using them in a menu list doesn't add a query per
item. Escape text values (`{{ }}`) unless you deliberately store HTML.

Codes only need to be unique per record type, so menu items and locations can both have a `badge`
field. A code can't clash with the record's own fields (like `menu_name`).

## Upgrading from v3

Run `php artisan igniter:up`. Existing fields and values are converted automatically:

- Field types are renamed: String becomes Text, Int becomes Number and Bool becomes On/off.
- Your slugs become codes unchanged, so `getExtraVarValue('your_slug')` keeps working.
- Values of fields that were deleted in v3 (left behind in the database) are removed.
- If two fields on the same record type shared a slug, the later one gets `_2` appended. v3 only
  ever read the first one.
- Values are now typed (Number returns a number, On/off returns `true`/`false`) and unset values
  return `null` instead of `''`. Check any template code that compares against strings.
- Text values are no longer limited to 255 characters.

## License

MIT. See [LICENSE](LICENSE).
