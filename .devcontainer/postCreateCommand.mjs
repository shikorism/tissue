import { spawn } from 'node:child_process';
import fs from 'node:fs';

const envPath = '.env';
const envExamplePath = '.env.example';

const spawnWithLog = async (command, args, options) => {
    if (!options.silent) {
        console.log(`Executing: ${command} ${args.join(' ')}`);
    }
    return new Promise((resolve, reject) => {
        let output = '';
        const childProcess = spawn(command, args, {
            ...options,
            env: {
                ...process.env,
                FORCE_COLOR: process.env.FORCE_COLOR || '1',
            },
            stdio: ['inherit', 'pipe', 'pipe']
        });

        childProcess.stdout.on('data', (data) => {
            const str = data.toString();
            if (!options.silent) {
                process.stdout.write(str);
            }
            output += str;
        });

        if (!options.silent) {
            childProcess.stderr.on('data', (data) => {
                const str = data.toString();
                process.stderr.write(str);
            });
        }

        childProcess.on('close', (code) => {
            if (code === 0) {
                resolve(output);
            } else {
                reject(new Error(`Process exited with code ${code}`));
            }
        });
    });
};

const waitDb = async () => {
    console.log('Waiting for database...');
    while (true) {
        try {
            const db = await spawnWithLog('php', ['artisan', 'db:monitor'], { shell: true, encoding: 'utf8', silent: true });
            if (db.includes('OK')) {
                break;
            }
        } catch (e) {
        }
        await new Promise(resolve => setTimeout(resolve, 1000));
    }
};

let envExists = true;
if (!fs.existsSync(envPath)) {
    envExists = false;
    fs.copyFileSync(envExamplePath, envPath);
    console.log('Created .env file from .env.example');
}

await spawnWithLog('composer', ['install'], { shell: true, encoding: 'utf8' });

if (fs.readFileSync(envPath, 'utf8').match(/^APP_KEY=$/m)) {
    await spawnWithLog('php', ['artisan', 'key:generate', '-n'], { shell: true, encoding: 'utf8' });
}

await waitDb();
await spawnWithLog('php', ['artisan', 'migrate', '-n'], { shell: true, encoding: 'utf8' });
await spawnWithLog('php', ['artisan', 'db:seed', '-n'], { shell: true, encoding: 'utf8' });

if (!envExists) {
    // .envがないのにDB上にはある場合、強制的に再生成した方が恐らく楽
    const passportOutput = await spawnWithLog('php', ['artisan', 'passport:install', '-n', '--force', '--no-ansi'], { shell: true, encoding: 'utf8' });

    const personalAccessMatch = passportOutput.match(/Personal access client.*?Client ID.*?(\d+).*?Client secret.*?([A-Za-z0-9]+)/s);
    const passwordGrantMatch = passportOutput.match(/Password grant client.*?Client ID.*?(\d+).*?Client secret.*?([A-Za-z0-9]+)/s);
    if (personalAccessMatch && passwordGrantMatch) {
        const personalAccessClientId = personalAccessMatch[1];
        const personalAccessClientSecret = personalAccessMatch[2];

        const envContent = fs.readFileSync(envPath, 'utf8');
        const newEnvContent = envContent +
            `\nPASSPORT_PERSONAL_ACCESS_CLIENT_ID=${personalAccessClientId}` +
            `\nPASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=${personalAccessClientSecret}`;

        fs.writeFileSync(envPath, newEnvContent);
        console.log('Added Passport configuration to .env file');
    } else {
        throw new Error('Failed to install Passport');
    }
}

await spawnWithLog('chown', ['-R', 'www-data', '/var/www/html/storage'], { shell: true, encoding: 'utf8' });

console.log('Setup completed successfully!');
console.log("You can access the application at http://localhost:4545 .");
