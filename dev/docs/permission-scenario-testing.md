# Permission Scenario Testing

Due to complexity that can arise in the various combinations of permissions, this document details scenarios and their expected results.

Test cases are written ability abstract, since all abilities should act the same in theory. Functional test cases may test abilities separate due to implementation differences.

Tests are categorised by the most specific element involved in the scenario, where the below list is most specific to least:

- Role entity permissions.
- Fallback entity permissions.
- Role permissions.

## General Permission Logical Rules

The below are some general rules we follow to standardise the behaviour of permissions in the platform:

- Most specific permission application (as above) take priority and can deny less specific permissions.
- Parent role entity permissions that may be inherited, are considered to essentially be applied on the item they are inherited to unless a lower level has its own permission rule for an already specific role.
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

#### test_71_multi_role_inheriting_deny_on_own

- Page permissions have inherit enabled.
- Role A has own page role permission.
- Role B has entity denied page permission.
- User has Role A and B.
- Use owns Page.

User denied page permission.

#### test_75_multi_role_inherited_deny_via_parent

- Page permissions have inherit enabled.
- Chapter permissions have inherit enabled.
- Role A has all-pages role permission.
- Role B has entity denied chapter permission.
- User has Role A & B.

User denied page permission.

#### test_76_multi_role_inherited_deny_via_parent_on_own

- Page permissions have inherit enabled.
- Chapter permissions have inherit enabled.
- Role A has own page role permission.
- Role B has entity denied chapter permission.
- User has Role A & B.

User denied page permission.

#### test_80_fallback_override_allow

- Page permissions have inherit disabled.
- Page fallback has entity deny permission.
- Role A has entity allow page permission.
- User has Role A.

User granted page permission.

#### test_81_fallback_override_deny

- Page permissions have inherit disabled.
- Page fallback has entity allow permission.
- Role A has entity deny page permission.
- User has Role A.

User denied page permission.

#### test_84_fallback_override_allow_multi_role

- Page permissions have inherit disabled.
- Page fallback has entity deny permission.
- Role A has entity allow page permission.
- Role B has no entity page permissions.
- User has Role A & B.

User granted page permission.

#### test_85_fallback_override_deny_multi_role

- Page permissions have inherit disabled.
- Page fallback has entity allow permission.
- Role A has entity deny page permission.
- Role B has no entity page permissions.
- User has Role A & B.

User denied page permission.

#### test_86_fallback_override_allow_inherit

- Chapter permissions have inherit disabled.
- Page permissions have inherit enabled.
- Chapter fallback has entity deny permission.
- Role A has entity allow chapter permission.
- User has Role A.

User granted page permission.

#### test_87_fallback_override_deny_inherit

- Chapter permissions have inherit disabled.
- Page permissions have inherit enabled.
- Chapter fallback has entity allow permission.
- Role A has entity deny chapter permission.
- User has Role A.

User denied page permission.

#### test_88_fallback_override_allow_multi_role_inherit

- Chapter permissions have inherit disabled.
- Page permissions have inherit enabled.
- Chapter fallback has entity deny permission.
- Role A has entity allow chapter permission.
- Role B has no entity chapter permissions.
- User has Role A & B.

User granted page permission.

#### test_89_fallback_override_deny_multi_role_inherit

- Chapter permissions have inherit disabled.
- Page permissions have inherit enabled.
- Chapter fallback has entity allow permission.
- Role A has entity deny chapter permission.
- Role B has no entity chapter permissions.
- User has Role A & B.

User denied page permission.

#### test_90_fallback_overrides_parent_entity_role_deny

- Chapter permissions have inherit disabled.
- Page permissions have inherit disabled.
- Chapter fallback has entity deny permission.
- Page fallback has entity deny permission.
- Role A has entity allow chapter permission.
- User has Role A.

User denied page permission.

#### test_91_fallback_overrides_parent_entity_role_inherit

- Book permissions have inherit disabled.
- Chapter permissions have inherit disabled. 
- Page permissions have inherit enabled.
- Book fallback has entity deny permission.
- Chapter fallback has entity deny permission.
- Role A has entity allow book permission.
- User has Role A.

User denied page permission.