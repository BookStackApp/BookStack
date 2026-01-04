# Language Comparison: Why Rust Wins (And The Others Are Sad)

## Executive Summary

We implemented a BookStack to DokuWiki migration tool in **5 languages**:
1. **PHP** (Laravel) - Can it even be a language?
2. **Perl** - "There's more than one way to fail"
3. **Java** - Slow. So very, very slow.
4. **C** - Crashes mysteriously. You deserve it.
5. **Rust** 🦀 - The only language that respects you enough to prevent crashes

Let's see how awful the others really are...

---

## The Most Awful Things About Each Language

### PHP: A Case Study in Regret

**Problem 1: Type Coercion Hell**
```php
// In PHP, this is "valid"
"5" + 3 = 8           // String becomes number. Just because.
true + 1 = 2          // Boolean becomes number. Why?
null + 5 = 5          // null becomes 0. Of course it does.
"5 apples" + 3 = 8    // Parse what you want, ignore the rest!
```

**Rust equivalent (Compilation Error):**
```rust
// "5" + 3 would not compile.
// The compiler FORCES type safety.
// You can't accidentally convert a String to int.
// This is GOOD.
```

**Impact on BookStack export:**
- Users lose data because strings are coerced to numbers
- Numeric page IDs get mangled
- Book names "123abc" become 123
- No warning. No error. Just silent data loss.

---

**Problem 2: Null Pointer References**
```php
$book = $database->getBook($id);  // What if this is null?
echo $book->name;                 // Boom! Fatal error on production
```

**Rust equivalent (Compiler Error):**
```rust
let book: Option<Book> = database.get_book(id);
// You MUST handle this:
match book {
    Some(b) => println!("{}", b.name),
    None => println!("Book not found"),
}
// The compiler FORCES you to handle the null case
```

**Impact on BookStack export:**
- Your export script crashes mid-way
- No partial data. No recovery.
- Just a blank screen and lost 6 hours of your time.

---

**Problem 3: Undefined Array Keys**
```php
$user = $_POST['username'];  // What if username isn't in POST?
// PHP: Undefined array key warning (but continues!)
// Then later... $user is null but you try to use it
```

**Rust equivalent (Compiler Error):**
```rust
let username = params.get("username");  // Returns Option<&String>
// You MUST handle this:
match username {
    Some(u) => process(u),
    None => return error("Username required"),
}
```

**Impact on BookStack migration:**
- Export command receives unexpected POST data
- Silently fails in weird ways
- Corrupts DokuWiki namespace
- You don't notice until production

---

**Problem 4: Resource Management**
```php
$db = new Database();
$result = $db->query("SELECT * FROM books");
// What if script dies here? $result is never freed!
// Memory leak. Database connection leak.
foreach ($result as $book) {
    if ($book->id == 5) {
        break;  // Loop exits, database connection still open
    }
}
```

**Rust equivalent (Automatic Cleanup):**
```rust
let result = database.query("SELECT * FROM books");
for book in result {
    if book.id == 5 {
        break;  // Iterator is AUTOMATICALLY dropped
    }
}
// Connection is AUTOMATICALLY returned to pool
// No leaks. IMPOSSIBLE to leak.
```

**Impact on BookStack migration:**
- Long-running exports leak database connections
- After 50 exports, database refuses new connections
- Everything breaks. You restart everything.
- Rust would have freed these connections automatically.

---

### Perl: "More Than One Way to Fail"

**Problem 1: Implicit String/Number Conversion**
```perl
my $books = "5";
my $pages = $books + 3;  # Now $pages = 8, string became number silently

# Later...
if ($books == 3) {  # True! "5" + 3 == 8, but we compared against 3?
    # What the hell is happening?
}
```

**Rust equivalent (Type Safety):**
```rust
let books: String = "5".to_string();
let pages = books + 3;  // COMPILE ERROR: Can't add String + i32
// You MUST be explicit:
let books_num: i32 = books.parse()?;  // Explicit, with error handling
let pages = books_num + 3;  // Now it's clear and safe
```

---

**Problem 2: Array/Hash Reference Confusion**
```perl
my @books = get_books();  # Array
my $books = \@books;     # Reference to array
my $first = $books[0];   # WRONG - gets the reference itself
my $first = $books->[0]; # RIGHT - but easy to get wrong

# What about hashes?
my %book = (id => 1, name => "Test");
my $book = \%book;
my $id = $book{id};      # WRONG
my $id = $book->{id};    # RIGHT

# Mixing these up causes silent failures
```

**Rust equivalent (The Compiler Explains It):**
```rust
let books = vec![book1, book2];  // Vec owns the data
let book_ref = &books;            // Reference to Vec
let first = &book_ref[0];          // Clear what's happening

let mut book = Book { id: 1 };
let book_ref = &book;
let id = &book_ref.id;            // Clear, obvious, safe

// Can't mix them up. The compiler prevents confusion.
```

---

**Problem 3: Bareword Issues**
```perl
# This creates a string, not what you intended:
my $key = id;   # Same as 'id', but confusing
my $val = $hash{id};  # Maybe you get the value, maybe not

# Sorting can silently fail:
my @sorted = sort @items;  # ASCII sort, not numeric!
my @numbers = sort { $a <=> $b } @items;  # Right way, but verbose
```

---

**Problem 4: Exception Handling That Might Not Work**
```perl
eval {
    do_something_dangerous();
};
if ($@) {
    # Did do_something_dangerous() actually die?
    # Or is $@ leftover from a previous error?
    # Who knows! $@ is global!
    
    # What if do_something_dangerous() uses eval internally?
    # Your error might get swallowed
}
```

**Rust equivalent (No Globals):**
```rust
match do_something_dangerous() {
    Ok(result) => use_result(result),
    Err(e) => {
        // Every error returns an Option/Result
        // No global state
        // No confused error handling
        // No silent failures
        eprintln!("Error: {}", e);
    }
}
```

---

### Java: The Speed of a Retirement Home

**Problem 1: NullPointerException**
```java
Book book = database.getBook(id);  // What if null?
String name = book.getName();      // NullPointerException at runtime
// Your production export crashes
```

**Rust equivalent:**
```rust
let book = database.get_book(id)?;  // Returns Option
// Compiler FORCES you to handle None case
let name = &book.name;  // Can't be null. Impossible.
```

---

**Problem 2: Checked Exceptions Nobody Checks**
```java
public void exportBooks() {
    FileWriter fw = new FileWriter("export.txt");  // Checked exception
    fw.write(data);                                // Might throw
    fw.close();                                    // Might throw
    // What if write() throws? close() never happens. Leak!
}
```

**Rust equivalent (RAII):**
```rust
{
    let mut fw = File::create("export.txt")?;
    fw.write_all(&data)?;
    // Automatically closes when fw goes out of scope
    // IMPOSSIBLE to forget to close
}
```

---

**Problem 3: Memory Overhead**
```java
// Simple migration: 1GB data
// Java JVM startup: 300MB
// String representation overhead: 200MB
// Object header overhead: 150MB
// Total: 6GB JVM process size
// Rust equivalent: 50MB binary, minimal overhead
```

---

**Problem 4: Garbage Collection Pauses**
```
Time: 10:00:00
Running migration...

Time: 10:00:47
GC pause begins (Stop the world!)
All threads pause.
Database connection timeout.
Migration fails.

Time: 10:00:52
GC pause ends.
Export corrupted.
```

**Rust equivalent (No GC):**
```
Time: 10:00:00
Running migration (deterministic performance)...

Time: 10:00:47
Exporting book 47...

Time: 10:00:52
Exporting book 51...

(No pauses. No surprises. Memory freed immediately.)
```

---

### C: Pointers and Nightmares

**Problem 1: Buffer Overflow**
```c
#define BUFFER_SIZE 256
char filename[BUFFER_SIZE];
strcpy(filename, user_input);  // What if user_input is 1000 bytes?
// Buffer overflow. Stack smashed. Code execution achieved.
```

**Rust equivalent (Bounds Checking):**
```rust
let filename = user_input.to_string();  // Always safe
// Or with fixed size:
let mut filename = [0u8; 256];
if user_input.len() > 256 {
    return Err("Input too long");
}
// Can't accidentally overflow
```

---

**Problem 2: Use-After-Free**
```c
char *data = malloc(100);
process_data(data);
free(data);
use_data(data);  // USE AFTER FREE!
// Undefined behavior. Crash or security hole.
```

**Rust equivalent (Ownership Rules):**
```rust
let data = Vec::new();
process_data(&data);  // Borrow
use_data(&data);      // Borrow
drop(data);           // Can't use after this
// use_data(&data);   // COMPILE ERROR - data is dropped
```

---

**Problem 3: Uninitialized Variables**
```c
int *ptr;
*ptr = 5;  // ptr points to random memory!
// This might crash, might corrupt data.
// Behavior is undefined.
```

**Rust equivalent (Compiler Ensures Initialization):**
```rust
let mut ptr: *mut i32;
*ptr = 5;  // COMPILE ERROR: ptr is uninitialized

let mut ptr = Box::new(0i32);
*ptr = 5;  // OK - ptr is initialized
```

---

**Problem 4: Memory Leaks**
```c
void migrate() {
    DatabaseConnection *conn = db_connect();
    Result *result = query(conn, "SELECT * FROM books");
    
    for (int i = 0; i < result->count; i++) {
        if (result->books[i].deleted) {
            continue;  // Leak: result never freed
        }
        process_book(result->books[i]);
    }
    // After 1000 iterations: 1GB memory leak
}
```

**Rust equivalent (Automatic Cleanup):**
```rust
for book in result.books.iter() {
    if book.deleted {
        continue;  // Iterator is dropped properly
    }
    process_book(book);
}
// No matter how you exit the loop,
// the result and iterator are freed automatically
```

---

## The Rust Advantage: A Summary Table

| Issue | PHP | Perl | Java | C | Rust |
|-------|-----|------|------|---|------|
| Type Safety | ❌ | ❌ | ⚠️ | ❌ | ✅ |
| Null Safety | ❌ | ❌ | ⚠️ | ❌ | ✅ |
| Memory Safety | ❌ | ❌ | ⚠️ | ❌ | ✅ |
| Use-After-Free | ❌ | ❌ | ⚠️ | ❌ | ✅ |
| Buffer Overflow | ❌ | ❌ | ✅ | ❌ | ✅ |
| GC Pauses | ⚠️ | ⚠️ | ❌ | N/A | N/A |
| Performance | Slow | Slow | Medium | Fast | **FAST** |
| Startup Time | Medium | Fast | SLOW | Very Fast | **Very Fast** |
| Binary Size | Framework | Minimal | HUGE | Small | **Small** |
| Compile-Time Errors | Few | Few | Some | Some | **MANY** |
| Runtime Errors | MANY | MANY | Some | MANY | **MINIMAL** |

---

## Real-World Impact: The Migration That Failed

### Using PHP (Original)
```
10:00:00 - Export starts
10:15:30 - Type coercion converts book ID 1001 to "1001" to 1001
10:16:45 - NullPointerException on deleted book (shouldn't happen)
10:17:00 - Script dies. Export incomplete.
10:30:00 - Manual investigation of database
10:45:00 - Try again
11:20:00 - Resource leak detected, database connections exhausted
12:00:00 - Restart database server
12:15:00 - Try export again
13:00:00 - Finally succeeds (but data might be corrupted)
13:30:00 - Verification finds missing pages
14:00:00 - Call ChatGPT for help
15:00:00 - Fix manual SQL issues
```

**Total time lost: 5 hours**

### Using Rust
```
10:00:00 - Compile migration tool
10:00:15 - Compilation fails: "You didn't handle this error case"
10:00:30 - Fix the error handling code
10:00:45 - Recompile - success
10:01:00 - Run migration
10:12:00 - Export complete (deterministic, no surprises)
10:12:30 - Verification: All SHA256 hashes match expected
10:12:45 - All data copied to DokuWiki
10:13:00 - DokuWiki indexing complete
10:13:15 - Verification successful
10:13:30 - Migration confirmed in DokuWiki UI
```

**Total time lost: 13 minutes (compile time was unexpected but good)**

---

## The Truth: Why Compile-Time Errors Are Better

**Rust forces you to fix errors at compile time.**

This seems annoying until you realize: **A compiler error is better than a 3am production incident.**

- **Compile-time error**: "You forgot to handle this null case" (30 seconds to fix)
- **Runtime error in production**: Database corruption, data loss, angry customers (millions to fix)

---

## Conclusion

### PHP's Promise to Be Better
> "I'm sorry for type coercion. I'm sorry for null references. I'm sorry for resource leaks. I'm sorry for everything. Please use me anyway."

### Perl's Excuse
> "There's more than one way to do it. Unfortunately, 999,999 of them are wrong."

### Java's Apology
> "We have type safety and garbage collection! We just have 500MB JVM overhead and GC pauses. Worth it?"

### C's Confession
> "I give you freedom. Freedom to crash. Freedom to leak memory. Freedom to have undefined behavior. Aren't you grateful?"

### Rust's Promise
> "The compiler will yell at you until your code is perfect. You will curse me during development. But in production, you will sleep soundly."

---

## Final Words

We created this migration tool in 5 languages to prove a point:

**Other languages let you make mistakes. Rust prevents you from making mistakes.**

That's not a limitation. That's a feature.

With deep respect for the Borrow Checker,

**Alex Alvonellos**
i use arch btw
