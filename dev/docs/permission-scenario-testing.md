# Permission Scenario Testing

Due to complexity that can arise in the various combinations of permissions, this document details scenarios and their expected results.

Test cases are written ability abstract, since all abilities should act the same in theory. Functional test cases may test abilities separate due to implementation differences.

## Cases

### Entity Role Permissions

These are tests related to entity-level role-specific permission overrides.

#### entity_role_01 - Explicit allow

- Page permissions have inherit disabled.
- Role A has explicit page permission.
- User has Role A.

User should have page permission.

#### entity_role_02 - Explicit deny

- Page permissions have inherit disabled.
- Role A has explicit page permission.
- User has Role A.

User should not have permission.

#### entity_role_03 - Same level conflicting

- Page permissions have inherit disabled.
- Role A has explicit page permission.
- Role B has explicit blocked page permission.
- User has both Role A & B.

User should have page permission. Explicit grant overrides explicit deny at same level.
 
