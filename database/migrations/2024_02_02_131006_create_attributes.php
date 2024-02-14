<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
    parent_attribute: attributes can optionally have a single parent
    user: can be one or none
    name: name of the attribute (unique to all attributes)
    description: some text explaining why this attribute is used, etc. : the author or other info
    is_retired: default false // if true then cannot be added to token types
    bounds:
      // each bounds can have max one type of bounds: location, time , path
      read_bounds: []
      write_bounds: []

    requirements:
      elements:
          required_siblings: [attribute ids] for sharing the same token type or token
          forbidden_siblings: [attribute ids] cannot be in the same token or type
      sets:
          allergies: [force_rules] cannot be in the same set if force rules apply
          affinities: [force_rules] this can only be in the set where force rules apply
    permissions:
      user_groups:
          usage: [] if empty then only the user's group can use this to create their types or add to tokens
          read: []  if empty anyone can read the attribute value
          write: [] if empty the admin group can change the attribute value.
      set_requirements:
          is_read_policy_all: bool
          is_write_policy_all: bool
          read: [] attribute ids  : based on policy, if one, then any matches in a set makes it readable, or all must match
          write: [] attribute ids : based on policy, if one, then any matches in a set makes it readable, or all must match
    value:
      value_type: (name of the type of data )
      min: (numeric only)
      max: (numeric only)
      regex: (string only can set enums here)
      default:
      allow_null: default true, but can only be false if the default is set
    options:
      constant:
      static: (can be static or constant)
      final:
      human:
meta:
            (can be in different langs or default lang)
            description: (in plain text or markup)
            name: (translate the name or a name that does not fit in the name rules for the legal name)
            standard_family: if named here this is a standard attribute
            author: who made this
            copywrite: any copywrite on the attribute content
            url: where to go for more info
            rating: marks adult or otherwise sensitive content (includes trigger warnings)


attributes:
    user_id : fk nullable

    read_bounds_schedule : fk nullable
    write_bounds_schedule : fk nullable
    read_bounds_map : fk nullable
    write_bounds_map : fk nullable
    read_bounds_shape : fk nullable
    write_bounds_shape : fk nullable

    is_retired: bool default false
    is_final: bool default false
    is_human: bool default false

    value_allow_null: bool default true
    value_type: string(20) see list
    value_numeric_min: number nullable
    value_numeric_max: number nullable
    value_regex: string nullable
    value_default: string nullable


    name string



attribute_force_rules:
        parent_attribute_id
        attribute_id:
        weight: integer:  (negative means more repulsed, positive means more attracted)
        numeric_min: number nullable
        numeric_max: number nullable
        string_value: (constant or regex)


attributes_requirements:
    parent_attribute_id
    attribute_id
    requirement_type read|write|required|forbidden

attribute_user_groups:
    parent_attribute_id
    user_group_id
    group_type: usage|read|write






 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('attributes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->default(null)
                ->comment("The owner of the attribute")
                ->index('idx_attribute_user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('parent_attribute_id')
                ->nullable()
                ->default(null)
                ->comment("The optional parent of the attribute")
                ->index('idx_attribute_parent_id')
                ->constrained('attributes')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();


            $table->foreignId('read_time_bounds_id')
                ->nullable()
                ->default(null)
                ->comment("reading this follows an optional schedule")
                ->index('idx_attribute_read_time_bounds_id')
                ->constrained('time_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('write_time_bounds_id')
                ->nullable()
                ->default(null)
                ->comment("writing this follows an optional schedule")
                ->index('idx_attribute_write_time_bounds_id')
                ->constrained('time_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('read_map_location_bounds_id')
                ->nullable()
                ->default(null)
                ->comment("Reading this depends on an optional map location")
                ->index('idx_attribute_read_map_bounds_id')
                ->constrained('location_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('write_map_location_bounds_id')
                ->nullable()
                ->default(null)
                ->comment("Writing this depends on an optional map location")
                ->index('idx_attribute_write_map_bounds_id')
                ->constrained('location_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('read_shape_location_bounds_id')
                ->nullable()
                ->default(null)
                ->comment("Reading this depends on an optional position inside a shape")
                ->index('idx_attribute_read_shape_bounds_id')
                ->constrained('location_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('write_shape_location_bounds_id')
                ->nullable()
                ->default(null)
                ->comment("Writing this depends on an optional position inside a shape")
                ->index('idx_attribute_write_shape_bounds_id')
                ->constrained('location_bounds')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->uuid('ref_uuid')
                ->unique()
                ->nullable(false)
                ->comment("used for display and id outside the code");

            $table->boolean('is_retired')->default(false)->nullable(false)
                ->comment('if true then cannot be added to types or new live-attribute on elements');


            $table->boolean('is_final')->default(false)->nullable(false)
                ->comment('if true then cannot be used as a parent');

            $table->boolean('is_standard')->default(false)->nullable(false)
                ->index('idx_is_standard')
                ->comment('if true then this attribute is a standard attribute or derived from one');


            $table->boolean('is_human')->default(false)->nullable(false)
                ->comment('if true then shows up in a human friendly filter');

            $table->boolean('is_read_policy_all')->default(false)->nullable(false)
                ->comment('if true then all the attibutes need to be in the set to read this in a set context');

            $table->boolean('is_write_policy_all')->default(false)->nullable(false)
                ->comment('if true then all the attibutes need to be in the set to write this in a set context');

            $table->boolean('is_nullable')->default(true)->nullable(false)
                ->comment('if true then value is nullable');




        });


        DB::statement("CREATE TYPE type_of_attribute_value AS ENUM (
            'numeric' , 'numeric_integer','numeric_natural',
            'string','json','string_markdown','string_html','string_xml','string_binary',
            'user','user_group','attribute','element','element_type',
            'remote','action','search',
            'schedule_bounds','map_bounds','shape_bounds',
            'view','mutual','container',
            'coordinate_map','coordinate_shape'
            );");

        DB::statement("ALTER TABLE attributes Add COLUMN value_type type_of_attribute_value NOT NULL default 'string';");

        Schema::table('attributes', function (Blueprint $table) {


            $table->float('value_numeric_min')->nullable()->default(null)
                ->comment("if set and this value type is number, then this is the min allowed for the value");

            $table->float('value_numeric_max')->nullable()->default(null)
                ->comment("if set and this value type is number, then this is the max allowed for the value");


            $table->jsonb('value_default')->nullable()->default(null)
                ->comment("if set this is default, cast to the type, when attribute is applied to type or live");

            $table->string('attribute_name',128)->nullable(false)->index()
                ->comment("The unique name of the attribute, using the naming rules");

            $table->string('value_regex')->nullable()->default(null)
                ->comment("if set and this is plain string, then regex filters this");

            $table->unique(['user_id','attribute_name']);
            $table->timestamps();
        });



        DB::statement('ALTER TABLE attributes ALTER COLUMN ref_uuid SET DEFAULT uuid_generate_v4();');


        DB::statement("ALTER TABLE attributes ALTER COLUMN created_at SET DEFAULT NOW();");

        DB::statement("
            CREATE TRIGGER update_modified_time BEFORE UPDATE ON attributes FOR EACH ROW EXECUTE PROCEDURE update_modified_column();
        ");
    } //up

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attributes');
        DB::statement("DROP TYPE type_of_attribute_value;");
    }
};
