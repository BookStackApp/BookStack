# Permission Scenario Testing

Due to complexity that can arise in the various combinations of permissions, this document details scenarios and their expected results.

Test cases are written ability abstract, since all abilities should act the same in theory. Functional test cases may test abilities separate due to implementation differences.

## Cases

TODO - User permissions
TODO - Role & entity-role interplay
TODO - entity-user permissions
TODO - entity-user & entity-role interplay

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