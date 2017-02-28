<?php
/**
 * @author Vincent Petry <pvince81@owncloud.com>
 *
 * @copyright Copyright (c) 2016, ownCloud GmbH
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\CustomGroups\Dav;

use Sabre\DAV\ICollection;
use Sabre\DAV\Exception\NotFound;
use Sabre\DAV\Exception\MethodNotAllowed;

use OCA\CustomGroups\CustomGroupsDatabaseHandler;
use OCA\CustomGroups\Search;

/**
 * Collection of custom groups
 */
class UserGroupsCollection extends GroupsCollection {

	/**
	 * User id for which to use memberships or null for all groups
	 *
	 * @var string
	 */
	protected $userId;

	/**
	 * Constructor
	 *
	 * @param CustomGroupsDatabaseHandler $groupsHandler custom groups handler
	 * @param MembershipHelper $helper helper
	 */
	public function __construct(
		CustomGroupsDatabaseHandler $groupsHandler,
		MembershipHelper $helper,
		$userId = null
	) {
		parent::__construct($groupsHandler, $helper);
		$this->userId = $userId;
	}

	/**
	 * Search nodes
	 *
	 * @param Search $search search
	 */
	public function search(Search $search = null) {
		$groups = $this->groupsHandler->getUserMemberships($this->userId, $search);
		return array_map(function ($groupInfo) {
			return $this->createMembershipsCollection($groupInfo);
		}, $groups);
	}

	/**
	 * Returns the name of the node.
	 *
	 * This is used to generate the url.
	 *
	 * @return string node name
	 */
	public function getName() {
		return $this->userId;
	}

	/**
	 * Not supported
	 *
	 * @param string $name name
	 * @throws MethodNotAllowed not supported
	 */
	public function createDirectory($name) {
		throw new MethodNotAllowed('Cannot create regular nodes');
	}
}
