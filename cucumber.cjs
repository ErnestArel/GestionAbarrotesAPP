module.exports = {
  default: {
    paths: ['testing/features/**/*.feature'],
    require: [
      'testing/support/**/*.cjs',
      'testing/steps/**/*.cjs'
    ],
    format: [
      'progress',
      'html:testing/reports/cucumber-report.html',
      'json:testing/reports/cucumber-report.json'
    ],
    parallel: 1
  }
};
