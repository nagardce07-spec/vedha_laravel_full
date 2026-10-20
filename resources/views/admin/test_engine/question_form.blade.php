<label>Question ID (manual)</label><input name="question_id" maxlength="100" required placeholder="e.g. TNPSC-GK-0001">
<label>Category</label><select name="category_id" id="addCategoryId" required><option value="">Select Category</option>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
<label>Topic</label><select name="topic_id" id="addTopicId" required><option value="">Select Topic</option>@foreach($topics as $t)<option value="{{ $t->id }}" data-category="{{ $t->category_id }}">{{ $t->name }}</option>@endforeach</select>
<label>Question Type</label><select name="question_type" id="addQuestionType"><option value="mcq">MCQ</option><option value="true_false">True / False</option></select>
<label>Question</label><textarea name="question_text" rows="4" required></textarea>
<div id="addOptions" style="display:grid;grid-template-columns:1fr 1fr;gap:10px"><div><label>Option A</label><input name="options[]" required></div><div><label>Option B</label><input name="options[]" required></div><div><label>Option C</label><input name="options[]"></div><div><label>Option D</label><input name="options[]"></div></div>
<label>Correct Answer (A / B / C / D)</label><input name="correct_answer" required>
<label>Explanation</label><textarea name="explanation" rows="3"></textarea>
<label>Difficulty</label><select name="difficulty" required><option value="easy">Easy</option><option value="medium" selected>Medium</option><option value="hard">Hard</option></select>
<label>Tags</label><input name="tags" placeholder="grammar, history">
<label>Image URL (optional)</label><input type="url" name="image_url">
<label><input type="checkbox" name="status" value="1" checked style="width:auto"> Active</label>
<script>
(function(){
 const cat=document.getElementById('addCategoryId'), topic=document.getElementById('addTopicId'), type=document.getElementById('addQuestionType'), opts=document.getElementById('addOptions');
 if(!cat||!topic||!type||!opts)return;
 function filterTopics(){let selected=cat.value;Array.from(topic.options).forEach(o=>{if(!o.value)return;o.hidden=!!selected&&o.dataset.category!==selected;if(o.hidden&&o.selected)topic.value='';});}
 function typeOptions(){const inputs=opts.querySelectorAll('input');if(type.value==='true_false'){opts.style.display='none';inputs.forEach(i=>i.required=false);}else{opts.style.display='grid';inputs.forEach((i,n)=>i.required=n<2);}}
 cat.addEventListener('change',filterTopics);type.addEventListener('change',typeOptions);filterTopics();typeOptions();
})();
</script>
