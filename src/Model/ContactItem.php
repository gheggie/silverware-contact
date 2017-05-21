<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Contact\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */

namespace SilverWare\Contact\Model;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverWare\Extensions\RenderableExtension;
use SilverWare\FontIcons\Extensions\FontIconExtension;
use SilverWare\Model\Component;
use SilverWare\ORM\MultiClassObject;
use SilverWare\View\GridAware;
use SilverWare\View\Renderable;
use SilverWare\View\ViewClasses;

/**
 * An extension of the multi-class object class for a contact item.
 *
 * @package SilverWare\Contact\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */
class ContactItem extends MultiClassObject
{
    use GridAware;
    use Renderable;
    use ViewClasses;
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Item';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Items';
    
    /**
     * Defines the default sort field and order for this object.
     *
     * @var string
     * @config
     */
    private static $default_sort = 'Sort';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Sort' => 'Int',
        'Title' => 'Varchar(255)',
        'HideTitle' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'Parent' => Component::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'HideTitle' => 0
    ];
    
    /**
     * Defines the summary fields of this object.
     *
     * @var array
     * @config
     */
    private static $summary_fields = [
        'Type',
        'Title',
        'Value',
        'Disabled.Nice'
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        FontIconExtension::class,
        RenderableExtension::class
    ];
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Create Field Objects:
        
        if ($this->isInDB()) {
            
            // Create Main Fields:
            
            $fields->addFieldToTab(
                'Root.Main',
                TextField::create(
                    'Title',
                    $this->fieldLabel('Title')
                )
            );
            
            // Create Options Fields:
            
            $fields->addFieldToTab(
                'Root.Options',
                CompositeField::create([
                    CheckboxField::create(
                        'HideTitle',
                        $this->fieldLabel('HideTitle')
                    )
                ])->setName('TitleOptions')->setTitle($this->fieldLabel('TitleOptions'))
            );
            
        }
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers a validator for the CMS interface.
     *
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return RequiredFields::create([
            'Title'
        ]);
    }
    
    /**
     * Answers the labels for the fields of the receiver.
     *
     * @param boolean $includerelations Include labels for relations.
     *
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        // Obtain Field Labels (from parent):
        
        $labels = parent::fieldLabels($includerelations);
        
        // Define Field Labels:
        
        $labels['Title'] = _t(__CLASS__ . '.TITLE', 'Title');
        $labels['Value'] = _t(__CLASS__ . '.VALUE', 'Value');
        $labels['HideTitle'] = _t(__CLASS__ . '.HIDETITLE', 'Hide title');
        $labels['TitleOptions'] = _t(__CLASS__ . '.TITLE', 'Title');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers the title of the receiver for the CMS interface.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getField('Title');
    }
    
    /**
     * Answers the value of the receiver for the CMS interface.
     *
     * @return string
     */
    public function getValue()
    {
        return null;
    }
    
    /**
     * Answers the default style ID for the HTML template.
     *
     * @return string
     */
    public function getDefaultStyleID()
    {
        return sprintf(
            '%s_%s',
            $this->Parent()->getHTMLID(),
            $this->getClassNameWithID()
        );
    }
    
    /**
     * Answers true if the title is to be shown in the template.
     *
     * @return boolean
     */
    public function getShowTitle()
    {
        return !$this->HideTitle;
    }
    
    /**
     * Answers the heading tag for the receiver.
     *
     * @return string
     */
    public function getHeadingTag()
    {
        return $this->Parent()->HeadingTag;
    }
    
    /**
     * Answers true to enable fixed width mode.
     *
     * @return boolean
     */
    public function getFontIconFixedWidth()
    {
        return true;
    }
    
    /**
     * Answers true if the font icon is to be shown.
     *
     * @return boolean
     */
    public function getShowIcon()
    {
        if ($parent = $this->Parent()) {
            return (boolean) $parent->ShowIcons;
        }
    }
    
    /**
     * Renders the object for the HTML template.
     *
     * @param string $layout Page layout passed from template.
     * @param string $title Page title passed from template.
     *
     * @return DBHTMLText|string
     */
    public function renderSelf($layout = null, $title = null)
    {
        return $this->customise([
            'Content' => $this->renderContent()
        ])->renderWith(self::class);
    }
    
    /**
     * Renders the content for the HTML template.
     *
     * @return DBHTMLText|string
     */
    public function renderContent()
    {
        return $this->renderWith(static::class);
    }
}
