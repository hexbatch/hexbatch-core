<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('elements', function (Blueprint $table) {

            $table->foreignId('element_parent_type_id')
                ->nullable()
                ->default(null)
                ->comment("The type that this element is made from")
                ->index('idx_element_parent_type_id')
                ->constrained('element_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('element_owner_user_id')
                ->nullable()
                ->default(null)
                ->comment("The owner of the remote")
                ->index('idx_element_owner_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->timestamps();
        });

        DB::statement('ALTER TABLE elements ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE elements ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON elements FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TRIGGER update_modified_time ON elements");

        Schema::table('elements', function (Blueprint $table) {
            $table->dropForeign(['element_parent_type_id']);
            $table->dropForeign(['element_owner_user_id']);

            $table->dropColumn('element_parent_type_id');
            $table->dropColumn('element_owner_user_id');

            $table->dropColumn('ref_uuid');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
};

// todo about the element value table, when a row is popped off due to an element coming back from the child set, the json can be optionally merged into its higher shell value, this is a table bool on the attribute (so adjust that table), but its marked on this table too, for lookup speed

// todo sets can be destroyed or created via api calls, and also by rules. If an element is already doing a set, that creation is ignored, likewise opposite.

// todo rules are actions, they can have a target attribute or type that the user making the rule has edit privilege . See below
/*
 * Add to rules:
 *  new types for targets: toggle|turn off|turn on , the target attribute or type's attributes, set to a constant,
 *       or set the target value with the value of another attribute (add new attribute to get that from, and a new json field for the constant)
 *
 *  new types for triggers: the attribute changes value (any value), be turned off, or turned on.
 *  Change name of existing target to trigger attribute.
 *  The trigger has to be something not whitelisted and the user is not on that . The target must be editable.
 *  Remotes can be linked together in a tree, with each node doing its own bool logic from the children (and or xor)
 * so add bool logic column to the remotes, as well as a parent remote.
 *  Rules in a tree do not have to do anything except for the root, but any node can do a regular rule if its bool from children is truthful
 *  Independent rules just listen to triggers
 *  Need new api to copy over all the rules from one attribute to another, and not just one at a time.
 *
 * Remote chains and unrelated can be saved per attribute, and that attribute copied.
 * Remote chains can listen to ancestors so can be reused for different things.
 */
