# Permission Scenario Testing

Due to complexity that can arise in the various combinations of permissions, this document details scenarios and their expected results.

Test cases are written ability abstract, since all abilities should act the same in theory. Functional test cases may test abilities separate due to implementation differences.

Tests are categorised by the most specific element involved in the scenario, where the below list is most specific to least:

- User entity permissions.
- Role entity permissions.
- Fallback entity permissions.
- Role permissions.

- TODO - Test fallback in the context of the above.

## General Permission Logical Rules

The below are some general rules we follow to standardise the behaviour of permissions in the platform:

- Most specific permission application (as above) take priority and can deny less specific permissions.
- Parent user/role entity permissions that may be inherited, are considered to essentially be applied on the item they are inherited to unless a lower level has its own permission rule for an already specific role/user.
- Where both grant and deny exist at the same specificity, we side towards grant.

## Cases

### Content Role Permissions

These are tests related to item/entity permissions that are set only at a role level.

#### test_01_allow

- Role A has role all-page permission.
- User has Role A.

User granted page permission.

#### test_02_deny

- Role A has no page permission.
- User has Role A.

User denied page permission.

#### test_10_allow_on_own_with_own

- Role A has role own-page permission.
- User has Role A.
- User is owner of page.

User granted page permission.

#### test_11_deny_on_other_with_own

- Role A has role own-page permission.
- User has Role A.
- User is not owner of page.

User denied page permission.

#### test_20_multiple_role_conflicting_all

- Role A has role all-page permission.
- Role B has no page permission.
- User has Role A & B.

User granted page permission.

#### test_21_multiple_role_conflicting_own

- Role A has role own-page permission.
- Role B has no page permission.
- User has Role A & B.
- User is owner of page.

User granted page permission.

---

### Entity Role Permissions

These are tests related to entity-level role-specific permission overrides.

#### test_01_explicit_allow

- Page permissions have inherit disabled.
- Role A has entity allow page permission.
- User has Role A.

User granted page permission.

#### test_02_explicit_deny

- Page permissions have inherit disabled.
- Role A has entity deny page permission.
- User has Role A.

User denied page permission.

#### test_03_same_level_conflicting

- Page permissions have inherit disabled.
- Role A has entity allow page permission.
- Role B has entity deny page permission.
- User has both Role A & B.

User granted page permission. 
Explicit grant overrides entity deny at same level.

#### test_20_inherit_allow

- Page permissions have inherit enabled.
- Chapter permissions has inherit disabled.
- Role A has entity allow chapter permission.
- User has Role A.

User granted page permission.

#### test_21_inherit_deny

- Page permissions have inherit enabled.
- Chapter permissions has inherit disabled.
- Role A has entity deny chapter permission.
- User has Role A.

User denied page permission.

#### test_22_same_level_conflict_inherit 

- Page permissions have inherit enabled.
- Chapter permissions has inherit disabled.
- Role A has entity deny chapter permission.
- Role B has entity allow chapter permission.
- User has both Role A & B.

User granted page permission.

#### test_30_child_inherit_override_allow

- Page permissions have inherit enabled.
- Chapter permissions has inherit disabled.
- Role A has entity deny chapter permission.
- Role A has entity allow page permission.
- User has Role A.

User granted page permission.

#### test_31_child_inherit_override_deny

- Page permissions have inherit enabled.
- Chapter permissions has inherit disabled.
- Role A has entity allow chapter permission.
- Role A has entity deny page permission.
- User has Role A.

User denied page permission.

#### test_40_multi_role_inherit_conflict_override_deny

- Page permissions have inherit enabled.
- Chapter permissions has inherit disabled.
- Role A has entity deny page permission.
- Role B has entity allow chapter permission.
- User has Role A & B.

User granted page permission.

#### test_41_multi_role_inherit_conflict_retain_allow

- Page permissions have inherit enabled.
- Chapter permissions has inherit disabled.
- Role A has entity allow page permission.
- Role B has entity deny chapter permission.
- User has Role A & B.

User granted page permission.

#### test_50_role_override_allow

- Page permissions have inherit enabled.
- Role A has no page role permission.
- Role A has entity allow page permission.
- User has Role A.

User granted page permission.

#### test_51_role_override_deny

- Page permissions have inherit enabled.
- Role A has no page-view-all role permission.
- Role A has entity deny page permission.
- User has Role A.

User denied page permission.

#### test_60_inherited_role_override_allow

- Page permissions have inherit enabled.
- Chapter permissions have inherit enabled.
- Role A has no page role permission.
- Role A has entity allow chapter permission.
- User has Role A.

User granted page permission.

#### test_61_inherited_role_override_deny

- Page permissions have inherit enabled.
- Chapter permissions have inherit enabled.
- Role A has page role permission.
- Role A has entity denied chapter permission.
- User has Role A.

User denied page permission.

#### test_62_inherited_role_override_deny_on_own

- Page permissions have inherit enabled.
- Chapter permissions have inherit enabled.
- Role A has own-page role permission.
- Role A has entity denied chapter permission.
- User has Role A.
- User owns Page.

User denied page permission.

#### test_70_multi_role_inheriting_deny

- Page permissions have inherit enabled.
- Role A has all page role permission.
- Role B has entity denied page permission.
- User has Role A and B.

User denied page permission.

#### test_80_multi_role_inherited_deny_via_parent

- Page permissions have inherit enabled.
- Chapter permissions have inherit enabled.
- Role A has all-pages role permission.
- Role B has entity denied chapter permission.
- User has Role A & B.

User denied page permission.

---

### Entity User Permissions

These are tests related to entity-level user-specific permission overrides.

#### test_01_explicit_allow

- Page permissions have inherit disabled.
- User has entity allow page permission.

User granted page permission.

#### test_02_explicit_deny

- Page permissions have inherit disabled.
- User has entity deny page permission.

User denied page permission.

#### test_10_allow_inherit

- Page permissions have inherit enabled.
- Chapter permissions have inherit disabled.
- User has entity allow chapter permission.

User granted page permission.

#### test_11_deny_inherit

- Page permissions have inherit enabled.
- Chapter permissions have inherit disabled.
- User has entity deny chapter permission.

User denied page permission.

#### test_12_allow_inherit_override

- Page permissions have inherit enabled.
- Chapter permissions have inherit disabled.
- User has entity deny chapter permission.
- User has entity allow page permission.

User granted page permission.

#### test_13_deny_inherit_override

- Page permissions have inherit enabled.
- Chapter permissions have inherit disabled.
- User has entity allow chapter permission.
- User has entity deny page permission.

User denied page permission.

#### test_40_entity_role_override_allow

- Page permissions have inherit disabled.
- User has entity allow page permission.
- Role A has entity deny page permission.
- User has role A.

User granted page permission.

#### test_41_entity_role_override_deny

- Page permissions have inherit disabled.
- User has entity deny page permission.
- Role A has entity allow page permission.
- User has role A.

User denied page permission.

#### test_42_entity_role_override_allow_via_inherit

- Page permissions have inherit enabled.
- Chapter permissions have inherit disabled.
- User has entity allow chapter permission.
- Role A has entity deny page permission.
- User has role A.

User granted page permission.

#### test_43_entity_role_override_deny_via_inherit

- Page permissions have inherit enabled.
- Chapter permissions have inherit disabled.
- User has entity deny chapter permission.
- Role A has entity allow page permission.
- User has role A.

User denied page permission.

#### test_50_role_override_allow

- Page permissions have inherit enabled.
- Role A has no page role permission.
- User has entity allow page permission.
- User has Role A.

User granted page permission.

#### test_51_role_override_deny

- Page permissions have inherit enabled.
- Role A has all-page role permission.
- User has entity deny page permission.
- User has Role A.

User denied page permission.

#### test_60_inherited_role_override_allow

- Page permissions have inherit enabled.
- Role A has no page role permission.
- User has entity allow chapter permission.
- User has Role A.

User granted page permission.

#### test_61_inherited_role_override_deny

- Page permissions have inherit enabled.
- Role A has view-all page role permission.
- User has entity deny chapter permission.
- User has Role A.

User denied page permission.

#### test_61_inherited_role_override_deny_on_own

- Page permissions have inherit enabled.
- Role A has view-own page role permission.
- User has entity deny chapter permission.
- User has Role A.
- User owns Page.

User denied page permission.

#### test_70_all_override_allow

- Page permissions have inherit enabled.
- Role A has no page role permission.
- Role A has entity deny page permission.
- User has entity allow page permission.
- User has Role A.

User granted page permission.

#### test_71_all_override_deny

- Page permissions have inherit enabled.
- Role A has page-all role permission.
- Role A has entity allow page permission.
- User has entity deny page permission.
- User has Role A.

User denied page permission.

#### test_80_inherited_all_override_allow

- Page permissions have inherit enabled.
- Role A has no page role permission.
- Role A has entity deny chapter permission.
- User has entity allow chapter permission.
- User has Role A.

User granted page permission.

#### test_81_inherited_all_override_deny

- Page permissions have inherit enabled.
- Role A has view-all page role permission.
- Role A has entity allow chapter permission.
- User has entity deny chapter permission.
- User has Role A.

User denied page permission.