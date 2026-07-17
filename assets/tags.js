const rawTags = [
  "tuxedo",
]

// Reduce the amount of human error: sort alphabetically and remove duplicates.
const tags = [...new Set(rawTags)].sort((a, b) => a.localeCompare(b));   
export { tags };