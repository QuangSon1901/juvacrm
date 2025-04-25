<?php
// 2023_04_26_000018_create_customers_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('phone', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('company', 255)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('type')->default(0)->comment('0 - Khách hàng tiềm năng, 1 - Khách hàng chưa sử dụng DV, 2 - Khách hàng đã sử dụng DV');
            $table->unsignedBigInteger('source_id')->nullable()->comment('tbl_customer_lead type=1');
            $table->bigInteger('service_usage_count')->default(0);
            $table->string('services', 255)->nullable()->comment('tbl_services');
            $table->unsignedBigInteger('class_id')->nullable()->comment('tbl_customer_class');
            $table->string('contact_methods', 100)->nullable()->comment('tbl_customer_lead type=0');
            $table->unsignedBigInteger('status_id')->nullable()->comment('tbl_customer_lead type=2');
            $table->string('note', 255)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->integer('is_active')->default(1);
            $table->integer('lead_score')->default(0)->comment('Điểm đánh giá tiềm năng (0-100)');
            $table->timestamp('last_interaction_date')->nullable()->comment('Thời điểm tương tác gần nhất với khách hàng');
            
            $table->index('type', 'idx_customer_type');
            $table->index('lead_score', 'idx_customer_lead_score');
            $table->index('last_interaction_date', 'idx_customer_last_interaction');
            $table->index('source_id', 'idx_customer_source');
            $table->index('status_id', 'idx_customer_status');
            $table->index('class_id', 'idx_customer_class');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_customers');
    }
}