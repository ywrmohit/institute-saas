<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Subscription Plans
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('billing_interval')->default('monthly'); // monthly, yearly
            $table->unsignedInteger('max_branches')->default(3);
            $table->unsignedInteger('max_students')->default(200);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Franchises (Primary Tenant)
        Schema::create('franchises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('logo')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('India');
            $table->string('tax_number')->nullable();
            $table->string('status')->default('active'); // active, suspended, pending
            $table->timestamp('plan_expires_at')->nullable();
            $table->unsignedInteger('max_students')->default(200);
            $table->unsignedInteger('max_branches')->default(3);
            $table->timestamps();
        });

        // 3. Branches (Sub-Tenant / Branch locations under franchise)
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained('franchises')->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();

            $table->unique(['franchise_id', 'code']);
        });

        // 4. Add Multi-Tenant & RBAC columns to Users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('franchise_id')->nullable()->after('id')->constrained('franchises')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->after('franchise_id')->constrained('branches')->nullOnDelete();
            $table->string('phone')->nullable()->after('email');
            $table->string('role')->default('franchise_owner')->after('password'); // super_admin, franchise_owner, branch_admin, trainer, accountant, student
            $table->string('status')->default('active')->after('role'); // active, inactive, suspended
            $table->string('avatar')->nullable()->after('status');
            $table->string('designation')->nullable()->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['franchise_id']);
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['franchise_id', 'branch_id', 'phone', 'role', 'status', 'avatar', 'designation']);
        });

        Schema::dropIfExists('branches');
        Schema::dropIfExists('franchises');
        Schema::dropIfExists('subscription_plans');
    }
};
