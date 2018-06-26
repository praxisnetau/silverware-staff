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
use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverWare\Extensions\Lists\ListViewExtension;
use SilverWare\Extensions\Model\ImageDefaultsExtension;
use SilverWare\Forms\FieldSection;
use SilverWare\Lists\ListSource;
use Page;

/**
 * An extension of the page class for a staff page.
 *
 * @package SilverWare\Staff\Pages
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-staff
 */
class StaffPage extends Page implements ListSource
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Staff Page';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Staff Pages';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'Holds a series of staff members organised into categories';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/staff: admin/client/dist/images/icons/StaffPage.png';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_StaffPage';
    
    /**
     * Defines the default child class for this object.
     *
     * @var string
     * @config
     */
    private static $default_child = StaffCategory::class;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
         'MemberSummary' => 'Varchar(16)'
    ];
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = [
        StaffCategory::class,
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
     * Defines the default values for the list view component.
     *
     * @var array
     * @config
     */
    private static $list_view_defaults = [
        'HideNoDataMessage' => 1
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
                    'StaffPageOptions',
                    $this->fieldLabel('StaffPage'),
                    [
                        DropdownField::create(
                            'MemberSummary',
                            $this->fieldLabel('MemberSummary'),
                            StaffCategory::singleton()->getMemberSummaryOptions()
                        )->setEmptyString(' ')->setAttribute('data-placeholder', $placeholder)
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
        $labels['StaffPage'] = _t(__CLASS__ . '.STAFFPAGE', 'Staff Page');
        $labels['MemberSummary'] = _t(__CLASS__ . '.MEMBERSUMMARY', 'Member summary');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers a list of members within the staff page.
     *
     * @return ArrayList
     */
    public function getMembers()
    {
        $members = ArrayList::create();
        
        $members->merge(StaffMember::get()->filter('ParentID', $this->AllChildren()->column('ID') ?: null));
        
        $members->merge($this->getChildMembers());
        
        return $members;
    }
    
    /**
     * Answers a list of the immediate child members of the staff page.
     *
     * @return DataList
     */
    public function getChildMembers()
    {
        return $this->AllChildren()->filter('ClassName', StaffMember::class);
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
     * Answers all categories within the receiver.
     *
     * @return DataList
     */
    public function getAllCategories()
    {
        return $this->AllChildren()->filter('ClassName', StaffCategory::class);
    }
    
    /**
     * Answers all visible categories within the receiver.
     *
     * @return ArrayList
     */
    public function getVisibleCategories()
    {
        $data = ArrayList::create();
        
        foreach ($this->getAllCategories() as $category) {
            
            $data->push(
                ArrayData::create([
                    'Title' => $category->Title,
                    'Category' => $category,
                    'Members' => $this->getMemberList($category)
                ])
            );
            
        }
        
        return $data;
    }
    
    /**
     * Answers the child member list component for the template.
     *
     * @return BaseListComponent
     */
    public function getChildMemberList()
    {
        $list = clone $this->getListComponent();
        
        $list->setSource($this->getChildMembers());
        $list->setStyleIDFrom($this);
        
        return $list;
    }
    
    /**
     * Answers the member list component for the given staff category.
     *
     * @param StaffCategory $category
     *
     * @return BaseListComponent
     */
    public function getMemberList(StaffCategory $category)
    {
        $list = clone $this->getListComponent();
        
        $list->setSource($category->getMembers());
        $list->setStyleIDFrom($this, $category->Title);
        
        return $list;
    }
}
