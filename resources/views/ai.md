# PRD-3: Employee SMS Report Access Seeder

## Objective

Create a **database seeder** to register access rights required for **Employee Reports** and **SMS Reports**, following the **existing access-right seeder structure** in the project.

---

## Tasks

### 1. Seeder Creation

* Create a new seeder file:

```
database/seeders/AccessRightSeeder.php
```

* Seeder name:

```
EmployeeSmsReportSeeder
```

* Seeder implementation **must follow existing access-right seeders** in the codebase.

---

### 2. Access Rights Scope

#### SMS Reports

* All reports from Revenue to SMS

#### Employee module

* Employee
* Employee attendance
* Employee review




## Acceptance Criteria

* Seeder runs without errors
* Required access rights are created
* No duplicate records created
* Existing permissions remain unchanged
