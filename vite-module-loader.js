import path from 'path';
import fs from 'fs/promises';

async function collectModuleAssetsPaths(paths, modulesPath) {
  modulesPath = path.join(__dirname, modulesPath);

  const moduleStatusesPath = path.join(__dirname, 'modules_statuses.json');
  let aliases = {};
  let concatFiles = [];
  let publicFiles = [];

  try {
    // Read module_statuses.json
    const moduleStatusesContent = await fs.readFile(moduleStatusesPath, 'utf-8');
    const moduleStatuses = JSON.parse(moduleStatusesContent);

    // Read module directories
    const moduleDirectories = await fs.readdir(modulesPath);

    for (const moduleDir of moduleDirectories) {
      if (moduleDir === '.DS_Store') {
        // Skip .DS_Store directory
        continue;
      }

      // Check if the module is enabled (status is true)
      if (moduleStatuses[moduleDir] === true) {
        const viteConfigPath = path.join(modulesPath, moduleDir, 'vite.config.js');
        const stat = await fs.stat(viteConfigPath);

        if (stat.isFile()) {
          // Import the module-specific Vite configuration
          const moduleConfig = await import(viteConfigPath);

          if (moduleConfig.paths && Array.isArray(moduleConfig.paths)) {
            paths.push(...moduleConfig.paths);
          }

          if (moduleConfig?.aliases) {
            aliases = {...aliases, ...moduleConfig.aliases};
          }

          if (moduleConfig?.concatFiles) {
            concatFiles.push(...moduleConfig.concatFiles);
          }

          if (moduleConfig?.publicFiles) {
            publicFiles.push(...moduleConfig.publicFiles);
          }
        }
      }
    }
  } catch (error) {
    console.error(`Error reading module statuses or module configurations: ${error}`);
  }

  return {paths, aliases, concatFiles, publicFiles};
}

export default collectModuleAssetsPaths;
