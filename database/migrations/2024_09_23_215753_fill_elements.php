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

// todo about the element value table, when a row is popped off due to an element coming back from the child set,
//  the json can be optionally merged into its higher shell value, this is a table bool on the attribute (so adjust that table), but its marked on this table too, for lookup speed

// todo sets can be destroyed or created via api calls, and also by rules. If an element is already doing a set, that creation is ignored, likewise opposite.



//todo special attributes update db status for mechanics when they are written to. This is how events mark the waiting items for completion
// here, the code will listen to changes on a special system attribute, when it writes attributes (an if statement)
// and each child attribute there will write to a specific column identified by its value (ref_uuid stored)
// so when something is pending, we have its ref and then update cancelled or approved for it
