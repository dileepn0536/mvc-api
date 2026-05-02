🏗️ Architecture Note: The Request Lifecycle
The goal of today’s learning was to see how a raw URL becomes a structured response. This is the Front Controller Pattern.

1. The Entry (The Kernel)
Your Project: index.php acts as the engine.

Concept: It is the Front Controller. Every request (GET, POST, etc.) must pass through this single file to ensure the environment is set up (Database, DI Container) before any logic runs.

Senior Insight: This centralizes security and error handling. Instead of exit or die, we catch exceptions and return a structured JSON error.

2. Dependency Inversion (DIP)
The Problem: Using the new keyword inside a class makes it "tightly coupled" and impossible to test.

The Solution: Use a DI Container and Reflection.

The Implementation: Your Container.php uses the Reflection API to "read" what a Controller needs and automatically injects those services.

DIP Rule: High-level modules (Controllers) should not depend on low-level modules (Database classes). Both should depend on Abstractions (Interfaces).

3. Real-Time Response Management
Current Logic: You are returning arrays from controllers and having index.php encode them.

Architecture Goal: Move toward a Response Object.

Why? A real-time API needs more than just data; it needs Meta-data. A Response Object allows you to control:

HTTP Status Codes (e.g., 201 for success, 422 for validation errors).

Headers (e.g., Content-Type, CORS, Caching).

Payload (The JSON data).

🛠️ Design Patterns Identified Today
In an interview, you can now say you’ve implemented these:

Dependency Injection Pattern: Automating object creation using a Container.

Singleton Pattern: Used in your Database::getInstance() to ensure only one connection exists.

Data Mapper/Repository Pattern: Decoupling the database queries from the Controller using UserRepository.