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
  //console.log('data:', data);
  //console.log('tags:', formTags);
  // Load the SQLite database and filter the results based on the form data
  if ( db == null) {
    db = await fetch('/assets/data.json')
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
    console.log('db: ', db);
  }
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
  //console.log('results: ', results);
  const resultsDiv = document.getElementById('face-container');
  let inHtml = ''
  if (results == 0 ) {
    inHtml = 'No results!';
  } else {
    results.forEach(item => {
      let thisHtml = '<h3>' + item.name + '</h3>'
      thisHtml += '<p>url: ' + item.url + '</p>'
      thisHtml += '<p>tag: ' + item.tags + '</p>'
      inHtml += thisHtml
    })
  }
  resultsDiv.innerHTML = inHtml;
}

form.addEventListener('submit', function(event) {
  event.preventDefault();
  getResults();
});

// Load results on select change
selects.forEach(select => {
  select.addEventListener('change', function(event) {
    //console.log(`Select changed: ${event.target.name} = ${event.target.value}`);
    getResults();
  });
});

// Load results on tag button change
tagbtns.forEach(btn => {
  btn.addEventListener('change', function(event) {
    console.log('change');
    getResults();
  })
})