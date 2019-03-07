#!/bin/sh
rm --force .env

if [ -z "${SLACK_KEY}" ]; then
    echo ""
    echo "Error: Missing Slack Api Key"
    echo "Fix: Run the container with the flag -e SLACK_KEY=your-slack-bot-oauth-key"
    exit 1
fi

if [ -z "${SLACK_CHANNEL}" ]; then
    echo ""
    echo "Error: Missing Slack Channel"
    echo "Fix: Run the container with the flag -e SLACK_CHANNEL=channel-name"
    exit 1
fi

if [ -z "${NOTIFY_LEVEL}" ]; then
    NOTIFY_LEVEL="0"
fi

if [ -z "${NOTIFY_DAYS}" ]; then
    NOTIFY_DAYS="all"
fi

if [ -z "${NOTIFY_LINES}" ]; then
    NOTIFY_LINES="all"
fi

echo "SLACK_KEY='${SLACK_KEY}'" >> .env
echo "SLACK_CHANNEL='${SLACK_CHANNEL}'" >> .env
echo "NOTIFY_LEVEL=${NOTIFY_LEVEL}" >> .env
echo "NOTIFY_DAYS=${NOTIFY_DAYS}" >> .env
echo "NOTIFY_LINES=${NOTIFY_LINES}" >> .env

echo "Environment generated OK."
exit 0
