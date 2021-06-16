# SilverStripe OEmbeddable

This module contains an OEmbeddable DataObject and a OEmbeddableField to easily add oEmbed content to a DataObject or Page. These were extracted, renamed & improved from [linkable](https://github.com/sheadawson/silverstripe-linkable) so they can be used on their own. (We use [NamedLinkField](https://github.com/restruct/silverstripe-namedlinkfield) module instead of Linkable).

## Example usage

Add a `$has_one` to `OEmbeddable` and insert `OEmbeddableField` to manage its contents.

```php
use Restruct\Silverstripe\OEmbedable\OEmbeddable;
use Restruct\Silverstripe\OEmbedable\OEmbeddableField;

class Page extends SiteTree
{
	private static $has_one = [
		'EmbedVideo' => OEmbeddable::class,
	];

	public function getCMSFields()
	{
		$fields = parent::getCMSFields();

		$fields->addFieldToTab(
		    'Root.Main',
		    OEmbeddableField::create(
		        'EmbedVideo',
		        'Video from oEmbed URL',
		        $this->EmbedVideo()
		    )
		);

		return $fields;
	}
}
```

In your template, you can render the object with the name of the has_one relation

```html
$EmbedVideo
```

You can also access other metadata on the object via

```html
<h1>$EmbedVideo.Title</h1>
$EmbedVideo.Description
$EmbedVideo.ThumbURL
```

## Switching from Linkable to OEmbeddable
Probably you should be able to use the upgrader tool, the new classnames are included in .upgrade.yml but I haven't tested this.

To upgrade manually, simply remove the Linkable module

### Keep Linkable module alongside OEmbeddable
**NOTE:** in case you're actually using the Linkable fields you may either just stay with that module OR change the `$table_name` for `OEmbeddable` (or the `EmbeddedObject` of Linkable). Then the two should be able to coexist peacefully alongside eachother. (In the SS4 updated version of Linkable we had to correct the value `$table_name` anyway).

You may **correct** the value of $db_field after a SS3->4 upgrade via Yaml config:
```yml
Sheadawson\Linkable\Models\EmbeddedObject:
  table_name: 'EmbeddedObject'
```

You may **update/change** its value (or that of OEmbeddable) to make the two work alongside:
```yml
Sheadawson\Linkable\Models\EmbeddedObject:
  table_name: 'LinkableEmbeddedObject' # this way OEmbeddable can use EmbeddedObject as table_name
```
**OR** change OEmbeddable to use a different table_name (remember to also correct that of Linkable):
```yml
Restruct\Silverstripe\OEmbedable\OEmbeddable:
  table_name: 'OEmbeddable'
```
