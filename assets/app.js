const form = document.getElementById('search-form');
const selects = form.querySelectorAll('select');
const tagbtns = document.querySelectorAll('.tag-btn input[type="checkbox"]');
let db = null

async function getResults() {
  // Read the form data and convert it to an object
  const formData = new FormData(form);
  const data = Object.fromEntries(formData.entries());
  let formTags = document.querySelectorAll('input[name="tags"]:checked');
  formTags = Array.from(formTags).map(cb => cb.value);
  // Load the SQLite database and filter the results based on the form data
  if ( db == null) {
    db = await fetch('/scryface/assets/data.json')
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
  }
  // Filter JSON "db" to get results
  let results = db.filter(item =>
    item.name.toLowerCase().includes(data.searchbar)
  );
  if (!data.gender == '') { results = results.filter(item => item.tags.includes(data.gender))}
  if (!data.eyecolor =='' ) { results = results.filter(item => item.tags.includes(data.eyecolor))}
  if (!data.haircolor =='' ) { results = results.filter(item => item.tags.includes(data.haircolor))}
  if (!data.skintone =='' ) { results = results.filter(item => item.tags.includes(data.skintone))}
  if (formTags.length > 0) {
    formTags.forEach(tag => {
      results = results.filter(item => item.tags.includes(tag));
    })
  }
  // Generate HTML results
  const resultsDiv = document.getElementById('face-container');
  let inHtml = ''
  if (data.gender == '' && data.eyecolor == '' && data.haircolor == '' && data.skintone == '' && formTags.length == 0 ) {
    // No search criteria specified
    // Remove this condition to show all results on blank form submission.
    inHtml = '<p>Select any item to show filtered results.</p>';
  } else if (results == 0 ) {
    // No matching results
    inHtml = '<p>No results!</p>';
  } else {
    // Show matching faces
    results.forEach(item => {
      let thisHtml = '<h3>' + item.name + '</h3>'
      thisHtml += '<p>url: ' + item.url + '</p>'
      thisHtml += '<p>tag: ' + item.tags + '</p>'
      inHtml += thisHtml
    })
  }
  resultsDiv.innerHTML = inHtml;
}

// Load results on form submit (prevent default)
form.addEventListener('submit', function(event) {
  event.preventDefault();
  getResults();
});

// Load results on select change
selects.forEach(select => {
  select.addEventListener('change', function(event) {
    getResults();
  });
});

// Load results on tag button change
tagbtns.forEach(btn => {
  btn.addEventListener('change', function(event) {
    getResults();
  });
});