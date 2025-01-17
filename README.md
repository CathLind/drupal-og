This is an adapted version of Organic Group, used for the BiC site.

## CHANGES FOR THE BiC SITE
- Worgroup: Creates a content type called workgroup and add three new roles 'Group manager', 'Group memeber' and 'Group guest'. This is the base for the Market module. Adds expirations date field for the user profile.
- Market: The main module for commerce logic. Creates a transaction type 'Piggy bank' attached to the Og group 'Workgroup'. Have commerce checkout pane for group creation and purchasing of 'credits' added to the group Piggy Bank.

## ADDED CODE

[WIP: Adds basic UI for adding, editing and removing group memberships #231](https://github.com/Gizra/og/pull/231)<br>
[426 Add my groups overview #566](https://github.com/Gizra/og/pull/566) (Removed since it created problem for us, and BiC don't need the functionality)<br> 
[254 Default argument for current user memberships #565](https://github.com/Gizra/og/pull/565)<br>
[Create og_perm views access plugin #300](https://github.com/Gizra/og/pull/300)<br>
[Group roles and permissions UI #243](https://github.com/Gizra/og/pull/243)<br>
[Leverage new core entityreference views filter #637](https://github.com/Gizra/og/pull/637)<br>
[Add a content listing route #423](https://github.com/Gizra/og/pull/423)<br>
[Create GroupMembership block condition plugin #351](https://github.com/Gizra/og/pull/351)<br>
[Add 'auto_add_group_owner_membership' setting #701](https://github.com/Gizra/og/pull/701)<br>
Remove any null values when building the list of target groups. Gizra#704<br>
<br>

## DESCRIPTION

The Organic Groups module (also referred to as the 'og' module), provides users
the ability to create, manage, and delete their own 'groups' on a site.
Each group can have members, and maintains a group home page which individual
group members may post into. Posts can be sent to multiple groups (i.e. cross-
posted), and individual posts (referred as 'group content') may be shared with
members, or non-members where necessary.
Group membership can be open, closed or moderated.

## TERMS AND DEFINITIONS

- GROUP: A single node which can have different content types and users
  associated with it.
- GROUP CONTENT: Content such as nodes or users, which are associated with a
  group.
- GROUP ADMIN: Is a privileged user with permission to administer particular
  activities within a group.
- SITE ADMIN: Compared to group admin, a site admin is granted access to all
  groups operating within a site. The site admin can specify the permissions
  group admins are granted in order to control their group related activities,
  while keeping other permissions out of their reach.
- GROUP CONTEXT: Whenever an individual piece of content such as a node or a
  user is viewed, the module attempts to determine if the content is associated
  with a particular group.
  The group context is later on used to determine which access rights the user
  is granted. For example, in a particular group context the user can edit
  nodes, but is only allowed to view the nodes in a different group context.
  The group context can also be used by custom modules to determine different
  behaviors. For example, displaying different blocks on different groups,
  switching to a different theme, etc.
- ENTITY: Nodes, users, and taxonomy terms, are examples of Drupal entities.
  Organic Groups allows each individual Drupal entity type to be associated with
  a group or with a group content. This means that you can associate different
  users (as group content) to a certain user (as a group).

## GROUP ARCHITECTURE

At the lowest level the module associates content types with groups. Above this
level is the role and permissions layer, which operates at the group level.
The Organic Groups module leverages Drupal's core functionality, especially the
field API. This means that a content type is associated with a group, by setting
the correct field value.
Users are also allowed to select the groups that will be associated with the
content from a list of groups, which they have authorization to view.
As is the case with Drupal itself, in Organic Groups different permissions can
be assigned to different user roles. This allows group members to perform a
different set of actions, in different group contexts.

### OG Membership Entity

The membership entity that connects a group and a user.

When dealing with non-user entities that are group content, that is content
that is associated with a group, we do it via an entity reference field that
has the default storage. The only information that we hold is that a group
content is referencing a group.

However, when dealing with the user entity we recognize that we need to
special case it. It won't suffice to just hold the reference between the user
and the group content as it will be laking crucial information such as: the
state of the user's membership in the group (active, pending or blocked), the
time the membership was created, the user's OG role in the group, etc.

For this meta data we have the fieldable OgMembership entity, that is always
connecting between a user and a group. There cannot be an OgMembership entity
connecting two non-user entities.

Creating such a relation is done for example in the following way:

```php
 $membership = Og::createMembership($entity, $user);
 $membership->save();
```

Notice how the relation of the user to the group also includes the OG
audience field name this association was done by. Like this we are able to
express different membership types such as the default membership that comes
out of the box, or a "premium membership" that can be for example expired
after a certain amount of time (the logic for the expired membership in the
example is out of the scope of OG core).

Having this field separation is what allows having multiple OG audience
fields attached to the user, where each group they are associated with may be
a result of different membership types.

## INSTALLATION DRUPAL 8.x
Note that the following guide is here to get you started. Names for content
types, groups and group content given here are suggestions and are given to
provide a quick way to get started with Organic groups.

1. Enable the Group and the Group UI modules.
2. Create a new content type via admin/structure/types/add. Call it "Group",
   and click on tab "Organic groups" then define it to be of Group type.
3. Create a second content type. Call it "Group content", and click on tab 
   "Organic groups" then set it to be of Group content type.
4. Add a Group by going to node/add/group. Call it First group.
5. Add a Group Content by going to node/add/group-content. In the Groups
   audience field, select First group. In the group content view a link was
   added to the group.
6. Click on the Group link. In the group view, a new tab was added labeled
   Group.
7. Click on the Group tab. You will be redirected to the group administration
   area. Note that this is the administration of First group only. It will not
   affect existing or new groups which will be created on the site.
8. You are now presented with different actions you can perform within the
   group. Such as add group members, add roles, and set member permissions. You
   will notice that these options have the same look and feel as Drupal core in
   matters relating to management of roles and permissions.
9. You can enable your privileged users to subscribe to a group by providing a
   'Subscribe' link. (Subscribing is the act of associating a user with a 
    group.)
   To show this subscribe link:
   1. Make sure you have the Group UI module enabled
   2. Go to admin/config/group/permissions and make sure that the "Subscribe 
       user to group" permission is given to the appropriate user-roles.
   3. Navigate to the "manage display" tab of your content type
      (admin/structure/types/manage/group/display)
       and choose the Group subscription format for the Group type field.
   4. Back in the group view you will now notice a 'Subscribe' link (If you are 
       the group administrator it will say "You are the group manager").
10. In order to associate other entities with group or group content, navigate
    to Organic Groups field settings", in admin/config/group/fields.
11. In order to define default permissions for groups that are newly created or
    to edit permissions on all existing groups, navigate to the Group
    default permissions page. Important permissions in this page are the ones
    under the administer section. These permissions are what enable group admins
    to have granular control over their own group. This means, that if you as
    the site admin, don't want to allow group admins to control who can edit
    nodes in their own group, you need to uncheck those permissions.

## DEVELOPERS & SITE BUILDERS

- Views integration: There are some default views that ship with the module.
  Follow those views configuration in terms of best practice (e.g. adding a
  relationship to the group-membership entity instead of querying directly the
  group-audience field).
- Token integration: Enable the entity-tokens module that ships with Entity API
  module.
- Rules integration: Organic groups is shipped with a Rules configuration that
  allows simple notification. You can disable it or clone and change its
  behaviour.
- Devel generate integration: Enable devel-generate module to create dummy
  groups and groups content.
- You may craft your own URLs to prepopulate the group-audience fields
  (e.g. node/add/post?field_group_audience=1 to prepopulate reference to
  node ID 1), using the "Entity reference prepopulate" module
  http://drupal.org/project/entityreference_prepopulate
  and configuring the correct settings in the field UI. Read more about
  it in Entity reference prepopulate's README file.
  Further more, when Entity reference prepopulate module is enabled the node
  "create" permissions will be enabled even for non-members. In order to allow
  a non member to create a node to a group they don't belong to, you should
  craft the URL in the same way. OG will recognize this situation and add the
  group as a valid option under the "My groups" widget.
- When deleting groups, it is possible to delete orphan group-content, or move
  it under another group. In order to do it in a scalable way, enable the
  "Use queue" option, and process it using for example:
  drush queue-run og_membership_orphans

## API

```php
use Drupal\og\Og;
use Drupal\og\OgGroupAudienceHelperInterface;

// Define the "Page" node type as a group.
Og::groupTypeManager()->addGroup('node', 'page');

// Add OG audience field to the "News" node type, thus making it group content.
Og::createField(OgGroupAudienceHelperInterface::DEFAULT_FIELD, 'node', 'news');
```

## Access control

See [Access control for groups and group content](docs/access.md).

## DRUPAL CONSOLE INTEGRATION
The Drupal 8 branch integrates with Drupal Console to do actions which used by
developers only. The supported actions are:
* Attaching OG fields to entities

**Please notice:** You need to install DrupalConsole 1.0.0-RC5 and above.

## FAQ

Q: How should I update from Drupal 6?
A: Run update.php; Enable the og-migrate module and execute all the migration
   plugins.

Q: How should I update from a previous Drupal 7 release (e.g. 7.x-1.0 to
   7.x-1.1)?
A: Same as updating from Drupal 6 -- Run update.php; If requested enable the
    og-migrate module and execute all the migration plugins.

Q: How do I use OG tokens with pathauto module to craft the url alias.
A: After enabling entity-tokens module you will have some tokens exposes by
   Organic groups. However you are not able to do something like
   [node:og_membership(1):group:label].
   See http://drupal.org/node/1088538#comment-4376910

Q: Must I use Panels module along with Organic groups?
A: No. However note that the maintainer of the module highly recommends using
   it, and considers it as good practice.

## CREDITS

* Organic groups for Drupal 5 and 6 authored by Moshe Weitzman -
  <weitzman AT tejasa DOT com>
* Current project maintainer and Drupal 7 author is Amitai Burstein (Amitaibu) -
  gizra.com
