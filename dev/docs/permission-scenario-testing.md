# Permission Scenario Testing

Due to complexity that can arise in the various combinations of permissions, this document details scenarios and their expected results.

Test cases are written ability abstract, since all abilities should act the same in theory. Functional test cases may test abilities separate due to implementation differences.

## Cases

### Entity Role Permissions

These are tests related to entity-level role-specific permission overrides.

#### test_01_explicit_allow

- Page permissions have inherit disabled.
- Role A has entity allow page permission.
- User has Role A.

User should have page permission.

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

User should have page permission. 
Explicit grant overrides entity deny at same level.

#### test_20_inherit_allow

- Page permissions have inherit enabled.
- Chapter permissions has inherit disabled.
- Role A has entity allow chapter permission.
- User has both Role A.

User should have page permission.

#### test_21_inherit_deny

- Page permissions have inherit enabled.
- Chapter permissions has inherit disabled.
- Role A has entity deny chapter permission.
- User has both Role A.

User denied page permission.

#### test_22_same_level_conflict_inherit 

- Page permissions have inherit enabled.
- Chapter permissions has inherit disabled.
- Role A has entity deny chapter permission.
- Role B has entity allow chapter permission.
- User has both Role A & B.

User should have page permission.

#### test_30_child_inherit_override_allow

- Page permissions have inherit enabled.
- Chapter permissions has inherit disabled.
- Role A has entity deny chapter permission.
- Role A has entity allow page permission.
- User has Role A.

User should have page permission.

#### test_31_child_inherit_override_deny

- Page permissions have inherit enabled.
- Chapter permissions has inherit disabled.
- Role A has entity allow chapter permission.
- Role A has entity deny page permission.
- User has Role A.

User denied page permission.