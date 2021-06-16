<?php

namespace Restruct\Silverstripe\OEmbedable;

use Embed\Embed;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataObjectInterface;
use SilverStripe\Core\Config\Config;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Security\SecurityToken;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\FormField;
use stdClass;

/**
 * Class OEmbeddableField
 * @license BSD License http://www.silverstripe.org/bsd-license
 * @author <marcus@silverstripe.com.au>
 */
class OEmbeddableField extends FormField
{
    /**
     * @var array
     */
    private static $allowed_actions = [
        'update',
    ];

    /**
     * @var bool
     */
    protected $editableEmbedCode = false;

    /**
     * @var mixed
     */
    protected $object;

    /**
     * @param mixed $value
     * @param null $data
     * @return $this|void
     */
    public function setValue($value, $data = null)
    {
        if ($value instanceof OEmbeddable) {
            $this->object = $value;
            parent::setValue($value->toMap());
        }

        parent::setValue($value);
    }

    /**
     * @param $code
     * @return $this
     */
    public function setEditableEmbedCode($code)
    {
        $this->editableEmbedCode = $code;

        return $this;
    }

    /**
     * @param array $properties
     * @return mixed|DBHTMLText
     */
    public function FieldHolder($properties = [])
    {
        Requirements::css('restruct/silverstripe-oembeddable:client/dist/styles/oembeddable.css');
        Requirements::javascript('restruct/silverstripe-oembeddable:client/dist/js/oembeddable.js');

//        if ($this->object && $this->object->ID) {
            $properties['SourceURL'] = TextField::create($this->getName() . '[sourceurl]', '')
                ->setAttribute('placeholder', _t('OEmbeddable.SOURCEURL', 'Source URL'));

            if (strlen($this->object->SourceURL)) {
                $properties['ObjectTitle'] = TextareaField::create(
                    $this->getName() . '[title]', _t('OEmbeddable.TITLE', 'Title')
                )->setRows(2);
                $properties['Width'] = TextField::create(
                    $this->getName() . '[width]', _t('OEmbeddable.WIDTH', 'Width')
                );
                $properties['Height'] = TextField::create(
                    $this->getName() . '[height]', _t('OEmbeddable.HEIGHT', 'Height')
                );
                $properties['ThumbURL'] = HiddenField::create($this->getName() . '[thumburl]', '');
                $properties['Type'] = HiddenField::create($this->getName() . '[type]', '');

                if ($this->editableEmbedCode) {
                    $properties['EmbedHTML'] = TextareaField::create(
                        $this->getName() . '[embedhtml]', 'Embed code'
                    );
                } else {
                    $properties['EmbedHTML'] = HiddenField::create($this->getName() . '[embedhtml]', '');
                }

                $properties['ObjectDescription'] = TextAreaField::create(
                    $this->getName() . '[description]', _t('OEmbeddable.DESCRIPTION', 'Description')
                )->setRows(2);
                $properties['ExtraClass'] = TextField::create(
                    $this->getName() . '[extraclass]', _t('OEmbeddable.CSSCLASS', 'CSS class')
                );

                foreach ($properties as $key => $field) {
                    if ($key == 'ObjectTitle') {
                        $key = 'Title';
                    } elseif ($key == 'ObjectDescription') {
                        $key = 'Description';
                    }

                    $field->setValue($this->object->$key);
                }

                if ($this->object->ThumbURL) {
                    $properties['ThumbImage'] = LiteralField::create(
                        $this->getName(), '<img src="' . $this->object->ThumbURL . '" />'
                    );
                }
            }
//        } else {
//            $properties['SourceURL'] = TextField::create($this->getName() . '[sourceurl]', '')
//                ->setAttribute('placeholder', _t('OEmbeddable.SOURCEURL', 'Source URL'));
//        }

        $field = parent::FieldHolder($properties);

        return $field;
    }

    /**
     * @param DataObjectInterface $record
     */
    public function saveInto(DataObjectInterface $record)
    {
        $val = $this->Value();
        $field = $this->getName() . 'ID';

        if (!strlen($val['sourceurl']) && $this->object) {
            if ($this->object->exists()) {
                $this->object->delete();
            }

            $record->$field = 0;

            return;
        }

        if (!$this->object) {
            $this->object = OEmbeddable::create();
        }

        $props = array_keys(Config::inst()->get(OEmbeddable::class, 'db'));
        foreach ($props as $prop) {
            $this->object->$prop = isset($val[strtolower($prop)]) ? $val[strtolower($prop)] : null;
        }

        $record->$field = $this->object->write();
    }

    /**
     * @param HTTPRequest $request
     * @return mixed|DBHTMLText|string
     */
    public function update(HTTPRequest $request)
    {
        if (!SecurityToken::inst()->checkRequest($request)) {
            return '';
        }

        $oEmbedUrl = $request->postVar('OEmbedURL');
        if (!filter_var($oEmbedUrl, FILTER_VALIDATE_URL)) {
            $this->object = null;
            $this->setMessage(
                _t('OEmbeddable.OEMBED_URL_INVALID', 'Could not inspect provided URL: ') . Convert::raw2xml($oEmbedUrl)
            );
        } elseif ($info = Embed::create($oEmbedUrl)) {
            $this->object = OEmbeddable::create();
            $this->object->setFromEmbed($info);
            // needed to make sure the check in FieldHolder succeed
            $this->object->ID = -1;
        } else {
            $this->object = null;
            $this->setMessage(
                _t('OEmbeddable.OEMBED_ERROR', 'Inspection did not return expected result.'),
                ValidationResult::TYPE_WARNING
            );
        }

        return $this->FieldHolder();
    }
}
