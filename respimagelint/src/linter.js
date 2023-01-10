//require("babelify/polyfill");
import linter from './linter/index';
import reporter from './reporter';

let data = JSON.parse(localStorage.respImageLintData);
data = linter(data);
console.log(data);
let report = reporter(data);

document.body.appendChild(report);
