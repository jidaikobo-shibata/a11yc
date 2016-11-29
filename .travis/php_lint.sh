#!/bin/bash
# thx http://c-note.chatwork.com/post/104035637770/travis-ci-php-%E3%81%AE-ci%E7%92%B0%E5%A2%83

IGNORE_PATTERN="vendor"
RESULT=$(find . -name "*.php" | grep -Ev "${IGNORE_PATTERN}" | xargs -I{} php -l {})

if [ $? -eq 0 ]; then
  echo "Success"
  exit 0
else
  echo "${RESULT}"
  exit 1
fi
