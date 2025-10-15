function generateHintText() {
  // pick approach: pronounceable or word list
  return generatePronounceableWords(60).join(' ');
}

// make pronounceable functions available (from snippet #2)
const CONSONANTS = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789~!@#$%^&*()-_=+[]{}|;:',.<>?/";
const VOWELS = "AEIOUaeiou";
function pronounceableWord(minLen = 3, maxLen = 7) {
  const length = Math.floor(Math.random() * (maxLen - minLen + 1)) + minLen;
  let word = "";
  let useConsonant = Math.random() < 0.6;
  for (let i = 0; i < length; i++) {
    word += (useConsonant ? CONSONANTS : VOWELS)[Math.floor(Math.random() * (useConsonant ? CONSONANTS.length : VOWELS.length))];
    useConsonant = !useConsonant;
  }
  return word;
}
function generatePronounceableWords(count = 1) {
  const arr = [];
  for (let i = 0; i < count; i++) arr.push(pronounceableWord());
  return arr;
}

// handler
document.addEventListener('keydown', function(e) {
  if (e.ctrlKey && (e.key === 'u' || e.key === 'U')) {
    e.preventDefault();
    const overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.left = 0;
    overlay.style.top = 0;
    overlay.style.width = '100%';
    overlay.style.height = '100%';
    overlay.style.background = '#000000ff';
    overlay.style.zIndex = 99999;
    overlay.style.padding = '0';
    overlay.style.overflow = 'auto';
    overlay.style.fontFamily = 'monospace';
    overlay.style.whiteSpace = 'pre-wrap';
    overlay.style.lineHeight = '1.6';
    overlay.textContent = generatePronounceableWords(5000).join(' ');
    document.body.appendChild(overlay);
    // remove overlay after a few seconds
    // setTimeout(() => overlay.remove(), 25000);
  }
});