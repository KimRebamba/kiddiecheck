@extends('teacher.layout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">
            Question {{ $questionIndex + 1 }}
        </h1>
        
        <div class="bg-white rounded-lg shadow-md p-6 max-w-2xl">
            <div class="mb-4">
                <p class="text-lg font-medium text-gray-700 mb-2">
                    {{ $question->text }}
                </p>
            </div>
            
            <div class="space-y-4">
                <form action="{{ route('teacher.tests.question.submit', [$test->test_id, $domainNumber, $questionIndex]) }}" method="POST">
                    @csrf
                    
                    <div class="flex flex-col space-y-4">
                        <div class="flex space-x-4">
                            <button type="submit" name="response" value="yes" 
                                class="px-6 py-3 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                Yes
                            </button>
                            <button type="submit" name="response" value="no" 
                                class="px-6 py-3 bg-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                No
                            </button>
                        </div>
                        
                        @if ($existingResponse)
                            <div class="mt-4 p-4 bg-blue-50 rounded-md">
                                <p class="text-sm text-blue-700">
                                    Current response: <strong>{{ $existingResponse }}</strong>
                                </p>
                            </div>
                        @endif
                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Notes (optional):
                            </label>
                            <textarea name="notes" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Add any notes about this response...">{{ $existingResponse->notes ?? '' }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="flex justify-between mt-6">
                <a href="{{ route('teacher.tests.question', [$test->test_id, $domainNumber, $maxIndex]) }}" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Skip to Last Question
                </a>
                
                <a href="{{ route('teacher.tests.form', $test->test_id) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Back to Test Form
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
