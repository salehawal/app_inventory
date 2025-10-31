# **PR Inventory System Architecture**

## **📋 Core Principles**

**Database-Driven Dynamic Inventory System** with **Location-Based Multi-Tenant Architecture** following **Mawal Class C Specification** (Usability First, Minimal Design, System Efficiency).

## **🏗️ Mawal Architecture Pattern: . → I → O → A → H**

### **. (Input/Source)**
- **Database Schema**: `pr_inventory` with `fict_` prefixed tables
- **Dynamic Discovery**: System scans `information_schema.tables` to auto-detect inventory categories
- **User Context**: Session-based authentication with location assignment

### **I (Identity/Features)**
- **Naming Convention**: `{prefix}_{category}` tables, `{prefix}_code` primary keys
- **Field Standards**: `_desc`, `_location`, `_cre_by`, `_cre_date`, `_remarks`
- **User-Location Mapping**: Multi-tenant access via `fict_user_locations`

### **O (Organization/Structure)**
- **Dynamic Pages**: Single codebase generates UI for all inventory types
- **Core Files**: `lib/core.php` (utilities), `lib/app.php` (business logic)
- **Auto-Generation**: Forms, tables, and menus created from database schema

### **A (Action/Application)**
- **CRUD Operations**: Consistent Create/Read/Update/Delete across all categories
- **Location Context**: All operations filtered by user's current location
- **Session Management**: PHP sessions with location-aware data access

### **H (Product/Integration)**
- **Unified Interface**: Same UI pattern for all inventory types
- **Scalable Design**: New categories added by creating database tables
- **Native Implementation**: No external frameworks, pure PHP/CSS/JavaScript

## **🗄️ Database Architecture Patterns**

### **Table Discovery Pattern**
```php
// Auto-detect inventory categories
$q = "SELECT table_name FROM information_schema.tables 
      WHERE table_schema = 'pr_inventory' AND table_name LIKE 'fict_%'";
```

### **Dynamic CRUD Pattern**
```php
// Single function handles all inventory types
function page_config($table) {
    $section = explode('_', $table)[1];  // Extract category name
    $prefix = explode('_', $first_field)[0];  // Extract field prefix
    // Generate forms/tables dynamically
}
```

### **Location-Aware Data Pattern**
```php
// All queries filtered by user location
$pdata['user']['current_location_code'] = $_SESSION['location'];
// Automatic location context in all operations
```

## **🔐 Security Architecture Patterns**

### **Multi-Layer Access Control**
1. **Authentication**: `fict_users` credential validation
2. **Authorization**: `fict_user_locations` access mapping
3. **Data Isolation**: Location-based query filtering
4. **Session Context**: PHP session with location state

### **Input Security Pattern**
```php
// Prepared statements for all database operations
$stmt = $pdo->prepare("SELECT * FROM {$table} WHERE {$field} = ?");
$stmt->execute([$clean_input]);
```

## **🎨 UI Generation Patterns**

### **Dynamic Interface Pattern**
- **Menu Generation**: Auto-populated from database table names
- **Form Generation**: Fields created from table schema
- **Table Rendering**: Columns auto-generated from database structure
- **Consistent Layout**: Same template for all inventory types

### **Native CSS Framework Pattern**
```
css/reset.css    - Normalization
css/native.css   - Core framework  
css/main.css     - Application styles
```

## **📱 Mawal Class C Implementation**

### **Usability First**
- **Single Interface**: Same UI for all inventory categories
- **Minimal Clicks**: Direct navigation to common tasks
- **Mobile Ready**: Responsive native CSS

### **Minimal Design**
- **No Heavy Frameworks**: Pure PHP/CSS/JavaScript
- **Essential Features Only**: Core inventory operations
- **Clean Interface**: Functional, non-decorative design

### **System Efficiency**
- **Code Reuse**: One codebase handles all inventory types
- **Database Driven**: Schema changes automatically reflected in UI
- **Performance**: Efficient queries with location-based filtering

## **🚀 Extension Pattern**

### **Adding New Inventory Categories**
1. **Create Table**: `CREATE TABLE fict_newcategory (...)`
2. **Follow Convention**: Use standard field prefixes
3. **Auto-Discovery**: System automatically detects and integrates
4. **No Code Changes**: UI generates automatically

### **Scalability Pattern**
- **Horizontal**: Add locations via `fict_location` table
- **Vertical**: Add categories via new `fict_*` tables  
- **Functional**: Extend with `page_static_config()` customizations

## **🔄 Core Workflow Pattern**

```
Login → Location Selection → Category Menu → Item Operations
  ↓         ↓                    ↓              ↓
Session   Location Context   Dynamic UI    Location-Filtered Data
```

## **🎯 Key Architectural Benefits**

1. **Dynamic Scalability**: New inventory types require only database table creation
2. **Multi-Tenant Ready**: Location-based data isolation
3. **Maintenance Efficiency**: Single codebase for all categories
4. **Consistent UX**: Uniform interface across all inventory types
5. **Security by Design**: Multi-layer access control with data isolation
6. **Framework Independence**: Native implementation without external dependencies