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

use SilverStripe\ORM\ArrayList;
use SilverStripe\View\ArrayData;
use SilverWare\Extensions\Lists\ListViewExtension;
use SilverWare\Extensions\Model\ImageDefaultsExtension;
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
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = [
        StaffCategory::class
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
     * Answers a list of members within the staff page.
     *
     * @return DataList
     */
    public function getMembers()
    {
        return StaffMember::get()->filter('ParentID', $this->AllChildren()->column('ID') ?: null);
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
     * Answers the member list component for the template.
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
    
    /**
     * Answers a message string to be shown when no data is available.
     *
     * @return string
     */
    public function getNoDataMessage()
    {
        return _t(__CLASS__ . '.NODATAAVAILABLE', 'No data available.');
    }
}
