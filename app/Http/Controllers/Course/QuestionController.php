<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Option;
use App\Models\Quiz;

class QuestionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'question_text' => 'required|string',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'required|string|max:255',
            'option_d' => 'required|string|max:255',
            'correct_answer' => 'required|in:a,b,c,d'
        ]);

        try {
            // Create question
            $question = Question::create([
                'quiz_id' => $request->quiz_id,
                'question_text' => $request->question_text,
            ]);


            // Create options
            $options = [
                ['question_id' => $question->id, 'option_key' => 'a', 'option_text' => $request->option_a, 'is_correct' => $request->correct_answer === 'a'],
                ['question_id' => $question->id, 'option_key' => 'b', 'option_text' => $request->option_b, 'is_correct' => $request->correct_answer === 'b'],
                ['question_id' => $question->id, 'option_key' => 'c', 'option_text' => $request->option_c, 'is_correct' => $request->correct_answer === 'c'],
                ['question_id' => $question->id, 'option_key' => 'd', 'option_text' => $request->option_d, 'is_correct' => $request->correct_answer === 'd']
            ];

            Option::insert($options);

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil ditambahkan',
                'data' => $question->load('options')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pertanyaan'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string|max:255',
            'option_b' => 'required|string|max:255',
            'option_c' => 'required|string|max:255',
            'option_d' => 'required|string|max:255',
            'correct_answer' => 'required|in:a,b,c,d'
        ]);

        try {
            $question = Question::findOrFail($id);

            // Update question
            $question->update([
                'question_text' => $request->question_text
            ]);

            // Delete old options and create new ones to ensure proper option_key
            $question->options()->delete();

            // Create new options
            $options = [
                ['question_id' => $question->id, 'option_key' => 'a', 'option_text' => $request->option_a, 'is_correct' => $request->correct_answer === 'a'],
                ['question_id' => $question->id, 'option_key' => 'b', 'option_text' => $request->option_b, 'is_correct' => $request->correct_answer === 'b'],
                ['question_id' => $question->id, 'option_key' => 'c', 'option_text' => $request->option_c, 'is_correct' => $request->correct_answer === 'c'],
                ['question_id' => $question->id, 'option_key' => 'd', 'option_text' => $request->option_d, 'is_correct' => $request->correct_answer === 'd']
            ];

            Option::insert($options);

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil diperbarui',
                'data' => $question->load('options')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pertanyaan'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $question = Question::findOrFail($id);
            $question->delete(); // Options akan otomatis terhapus karena cascade delete

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pertanyaan'
            ], 500);
        }
    }
}
