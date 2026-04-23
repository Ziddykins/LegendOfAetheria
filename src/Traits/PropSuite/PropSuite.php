<?php
    namespace Game\Traits\PropSuite;

    use Game\Traits\PropSuite\PropConvert;
    use Game\Traits\PropSuite\PropMod;
    use Game\Traits\PropSuite\PropSync;
    use Game\Traits\PropSuite\PropDump;

    /**
     * Complete property management suite combining database synchronization, conversions,
     * mathematical operations, and serialization for game entities.
     * 
     * Composed of:
     * - PropConvert: snake_case ↔ camelCase conversion for database ↔ PHP properties
     * - PropMod: Mathematical operations (add_, sub_, mul_, div_, exp_, mod_)
     * - PropSync: Database synchronization via magic methods (get_, set_, load_, new_)
     * - PropDump: Object serialization/deserialization with nested object support
     * 
     * Classes using this trait gain dynamic property access with automatic database persistence.
     */
    trait PropSuite {
        use PropConvert;
        use PropMod;
        use PropSync;
        use PropDump;
    }
?>