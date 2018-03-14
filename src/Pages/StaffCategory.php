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

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverWare\Extensions\Lists\ListViewExtension;
use SilverWare\Extensions\Model\ImageDefaultsExtension;
use SilverWare\Forms\FieldSection;
use SilverWare\Lists\ListSource;
use Page;

/**
 * An extension of the page class for a staff category.
 *
 * @package SilverWare\Staff\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-staff
 */
class StaffCategory extends Page implements ListSource
{
    /**
     * Define summary constants.
     */
    const SUMMARY_PROFILE   = 'Content';
    const SUMMARY_SUMMARY   = 'SummaryMeta';
    const SUMMARY_EDUCATION = 'Education';
    
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Staff Category';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Staff Categories';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A category within a staff page which holds a series of members';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/staff: admin/client/dist/images/icons/StaffCategory.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_StaffCategory';
    
    /**
     * Defines the default child class for this object.
     *
     * @var string
     * @config
     */
    private static $default_child = StaffMember::class;
    
    /**
     * Determines whether this object can exist at the root level.
     *
     * @var boolean
     * @config
     */
    private static $can_be_root = false;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'MemberSummary' => 'Varchar(16)',
        'ShowContent' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'ListInherit' => 1,
        'ShowContent' => 0,
        'MemberSummary' => 'MetaSummary',
        'HideFromMainMenu' => 1
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = [
        StaffMember::class
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        ListViewExtension::class,
        ImageDefaultsExtension::class
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
        
        // Define Placeholder:
        
        $placeholder = _t(__CLASS__ . '.DROPDOWNDEFAULT', '(default)');
        
        // Create Options Tab:
        
        $fields->findOrMakeTab('Root.Options', $this->fieldLabel('Options'));
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                FieldSection::create(
                    'StaffCategoryOptions',
                    $this->fieldLabel('StaffCategory'),
                    [
                        DropdownField::create(
                            'MemberSummary',
                            $this->fieldLabel('MemberSummary'),
                            $this->getMemberSummaryOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder),
                        CheckboxField::create(
                            'ShowContent',
                            $this->fieldLabel('ShowContent')
                        )
                    ]
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
        
        $labels['Options'] = _t(__CLASS__ . '.OPTIONS', 'Options');
        $labels['StaffCategory'] = _t(__CLASS__ . '.STAFFCATEGORY', 'Staff Category');
        $labels['MemberSummary'] = _t(__CLASS__ . '.MEMBERSUMMARY', 'Member summary');
        $labels['ShowContent'] = _t(__CLASS__ . '.SHOWCONTENTONSTAFFPAGE', 'Show content on staff page');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers a list of members within the staff category.
     *
     * @return DataList
     */
    public function getMembers()
    {
        return StaffMember::get()->filter('ParentID', $this->ID);
    }
    
    /**
     * Answers true if the receiver has at least one member.
     *
     * @return boolean
     */
    public function hasMembers()
    {
        return $this->getMembers()->exists();
    }
    
    /**
     * Answers a list of members within the receiver.
     *
     * @return DataList
     */
    public function getListItems()
    {
        return $this->getMembers();
    }
    
    /**
     * Answers true if the content of the category is to be shown on the staff page.
     *
     * @return boolean
     */
    public function getContentShown()
    {
        return ($this->Content && $this->ShowContent);
    }
    
    /**
     * Answers an array of options for the member summary field.
     *
     * @return array
     */
    public function getMemberSummaryOptions()
    {
        return [
            self::SUMMARY_PROFILE => _t(__CLASS__ . '.PROFILE', 'Profile'),
            self::SUMMARY_SUMMARY => _t(__CLASS__ . '.SUMMARY', 'Summary'),
            self::SUMMARY_EDUCATION => _t(__CLASS__ . '.EDUCATION', 'Education')
        ];
    }
}
