import { startStimulusApp } from '@symfony/stimulus-bridge';

// Initialize Stimulus app
const app = startStimulusApp(require.context(
    './controllers',
    true,
    /\.js$/
));

export { app };
