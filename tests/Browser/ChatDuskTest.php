<?php
namespace Namu\WireChat\Tests\Browser;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use Namu\WireChat\Livewire\Chat\Chat;
use Namu\WireChat\Livewire\Chat\ChatList;
use Namu\WireChat\Models\Conversation;
use Namu\WireChat\Models\Message;
use Namu\WireChat\Tests\DuskTestCase;
use Workbench\App\Models\User;

class ChatDuskTest extends DuskTestCase
{
    


    /** @test */
    public function it_can_show_conversation_when_header_dropdown_is_clicked()
    {

      //  dd(config('livewire.layout'));
        $auth = User::factory()->create();

        $receiver = User::factory()->create(['name'=> 'receiver']);


        $conversation=   $auth->createConversationWith($receiver,'hi');
        $conversationID = $conversation->id;
        

           // Create a new class that sets the conversation to 1
        $component = new class extends Chat {
            public $conversation = 2;
        };


        //dd($auth->belongsToConversation($conversation));
        info(['count: ChatDuskTest'=> Conversation::withoutGlobalScopes()->count()]);

        Livewire::actingAs($auth)->visit($component)->assertSee('receiver');



    }


     /** @test */
     public function it_can_show_correctly_formatted_time()
     {
 
        $auth = User::factory()->create();
        $receiver = User::factory()->create(['name' => 'John']);
        
        // Create a conversation with participants


        $conversation = $auth->createConversationWith($receiver);
    
        // Set specific times for testing purposes
        $todayTime = now()->setTime(13, 0); // Today at 1:00 PM
        $yesterdayTime = now()->subDay()->setTime(15, 0); // Yesterday at 3:00 PM
        $thisWeekTime = now()->subDays(2)->setTime(9, 0); // Two days ago at 9:00 AM
        $olderTime = now()->subWeeks(2)->setTime(10, 30); // Two weeks ago at 10:30 AM
        
        
        // Create messages with different timestamps
        $todayMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sendable_type' => get_class($auth),
            'sendable_id' => $auth->id,
            'body' => 'Message from today',
            'created_at' => $todayTime
        ]);

    
        $yesterdayMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sendable_type' => get_class($auth),
            'sendable_id' => $auth->id,
            'body' => 'Message from yesterday',
            'created_at' => $yesterdayTime
        ]);
    
        $thisWeekMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sendable_type' => get_class($auth),
            'sendable_id' => $auth->id,
            'body' => 'Message from this week',
            'created_at' => $thisWeekTime
        ]);
    
        $olderMessage = Message::create([
            'conversation_id' => $conversation->id,
            'sendable_type' => get_class($auth),
            'sendable_id' => $auth->id,
            'body' => 'Older message',
            'created_at' => $olderTime
        ]);
    
        // Expected outputs based on the message created_at timestamps
        $todayExpected = $todayTime->format('g:i A'); // e.g., "1:00 PM"
        $yesterdayExpected = 'Yesterday ' . $yesterdayTime->format('g:i A'); // e.g., "Yesterday 3:00 PM"
        $thisWeekExpected = $thisWeekTime->format('D g:i A'); // e.g., "Mon 9:00 AM"
        $olderExpected = $olderTime->format('m/d/y'); // e.g., "08/31/24"
        
        $component = new class extends Chat {
            public $conversation = 2;
        };


        // Run the test
        Livewire::actingAs($auth)
            ->visit($component)
            ->assertSee($todayExpected)        // Assert "1:00 PM"
            ->assertSee($yesterdayExpected)    // Assert "Yesterday 3:00 PM"
            ->assertSee($thisWeekExpected)     // Assert "Mon 9:00 AM" (or whatever day it is)
            ->assertSee($olderExpected)        // Assert "08/31/24"
            ->assertSee('Message from today')
            ->assertSee('Message from yesterday')
            ->assertSee('Message from this week')
            ->assertSee('Older message');
    
 
     }
        
}
