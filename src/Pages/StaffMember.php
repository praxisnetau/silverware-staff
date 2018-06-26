<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Staff\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-staff
 */

namespace SilverWare\Staff\Pages;

use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;
use SilverWare\Extensions\Model\DetailFieldsExtension;
use Page;

/**
 * An extension of the page class for a staff member.
 *
 * @package SilverWare\Staff\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-staff
 */
class StaffMember extends Page
{
    /**
     * Define gender constants.
     */
    const GENDER_MALE        = 'male';
    const GENDER_FEMALE      = 'female';
    const GENDER_UNSPECIFIED = 'unspecified';
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Staff Member';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Staff Members';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'An individual member within a staff category';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/staff: admin/client/dist/images/icons/StaffMember.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_StaffMember';
    
    /**
     * Determines whether this object can exist at the root level.
     *
     * @var boolean
     * @config
     */
    private static $can_be_root = false;
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Gender' => 'Varchar(32)',
        'Position' => 'Varchar(255)',
        'PostNominals' => 'Varchar(255)',
        'Education' => 'HTMLText'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'Gender' => 'unspecified',
        'ShowInMenus' => 0
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        DetailFieldsExtension::class
    ];
    
    /**
     * Defines the asset folder for uploaded meta images.
     *
     * @var string
     * @config
     */
    private static $meta_image_folder = 'Staff';
    
    /**
     * Defines the list item details to show for this object.
     *
     * @var array
     * @config
     */
    private static $list_item_details = [
        'date' => false,
        'position' => [
            'icon' => 'id-card-o',
            'text' => '$Position'
        ]
    ];
    
    /**
     * Defines the detail fields to show for the object.
     *
     * @var array
     * @config
     */
    private static $detail_fields = [
        'education' => [
            'name' => 'Education',
            'text' => '$Education'
        ]
    ];
    
    /**
     * Defines the setting for hiding the detail fields header.
     *
     * @var boolean
     * @config
     */
    private static $detail_fields_hide_header = true;
    
    /**
     * Defines the setting for using a heading tag for each detail field.
     *
     * @var boolean
     * @config
     */
    private static $detail_fields_use_heading = true;
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Modify Field Objects:
        
        $fields->dataFieldByName('Content')->setTitle($this->fieldLabel('Profile'));
        
        // Create Details Tab:
        
        $fields->findOrMakeTab('Root.Details', $this->fieldLabel('Details'));
        
        // Create Details Fields:
        
        $fields->addFieldsToTab(
            'Root.Details',
            [
                DropdownField::create(
                    'Gender',
                    $this->fieldLabel('Gender'),
                    $this->getGenderOptions()
                ),
                TextField::create(
                    'Position',
                    $this->fieldLabel('Position')
                ),
                TextField::create(
                    'PostNominals',
                    $this->fieldLabel('PostNominals')
                ),
                HTMLEditorField::create(
                    'Education',
                    $this->fieldLabel('Education')
                )
            ]
        );
        
        // Answer Field Objects:
        
        return $fields;
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
        
        $labels['Title'] = _t(__CLASS__ . '.NAME', 'Name');
        $labels['Gender'] = _t(__CLASS__ . '.GENDER', 'Gender');
        $labels['Details'] = _t(__CLASS__ . '.DETAILS', 'Details');
        $labels['Profile'] = _t(__CLASS__ . '.PROFILE', 'Profile');
        $labels['Position'] = _t(__CLASS__ . '.POSITION', 'Position');
        $labels['Education'] = _t(__CLASS__ . '.EDUCATION', 'Education');
        $labels['PostNominals'] = _t(__CLASS__ . '.POSTNOMINALS', 'Post-nominals');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers a string of CSS classes to apply to the receiver in the CMS tree.
     *
     * @return string
     */
    public function CMSTreeClasses()
    {
        $classes = parent::CMSTreeClasses();
        
        $classes .= sprintf(' gender-%s', $this->Gender);
        
        $this->extend('updateCMSTreeClasses', $classes);
        
        return $classes;
    }
    
    /**
     * Answers the parent category of the receiver.
     *
     * @return StaffCategory
     */
    public function getCategory()
    {
        return $this->getParent();
    }
    
    /**
     * Answers the meta summary for the receiver.
     *
     * @return DBHTMLText
     */
    public function getMetaSummary()
    {
        if ($parent = $this->getParent()) {
            
            if ($field = $parent->MemberSummary) {
                return $this->dbObject($field);
            }
            
        }
        
        return parent::getMetaSummary();
    }
    
    /**
     * Answers an array of options for the gender field.
     *
     * @return array
     */
    public function getGenderOptions()
    {
        return [
            self::GENDER_UNSPECIFIED => _t(__CLASS__ . '.UNSPECIFIED', 'Unspecified'),
            self::GENDER_FEMALE => _t(__CLASS__ . '.FEMALE', 'Female'),
            self::GENDER_MALE => _t(__CLASS__ . '.MALE', 'Male')
        ];
    }
}
