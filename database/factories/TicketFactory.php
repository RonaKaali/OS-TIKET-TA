<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\HelpTopic;
use App\Models\Priority;
use App\Models\SlaPlan;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        $department = Department::inRandomOrder()->first() ?? Department::factory()->create();
        $topic = HelpTopic::where('department_id', $department->id)->inRandomOrder()->first()
            ?? HelpTopic::factory()->create(['department_id' => $department->id]);
        $status = Status::inRandomOrder()->first() ?? Status::factory()->create();
        $priority = Priority::inRandomOrder()->first() ?? Priority::factory()->create();
        $sla = SlaPlan::inRandomOrder()->first() ?? SlaPlan::factory()->create();
        $user = User::factory()->create();

        $prefix = config('ticket.number_prefix', 'CSIRT');
        $random = strtoupper(Str::random(6));

        return [
            'uuid' => (string) Str::uuid(),
            'ticket_number' => "{$prefix}-{$random}",
            'subject' => fake()->sentence(4),
            'reporter_email' => $user->email,
            'reporter_name' => $user->name,
            'user_id' => $user->id,
            'department_id' => $department->id,
            'help_topic_id' => $topic->id,
            'priority_id' => $priority->id,
            'status_id' => $status->id,
            'sla_plan_id' => $sla->id,
            'due_at' => now()->addHours(48),
        ];
    }
}
