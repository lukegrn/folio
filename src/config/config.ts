import { LogLevel } from "tslog";

interface DatabaseConfig {
  host: string;
  port: number;
  database: string;
  username: string;
  password: string;
}

interface LogConfig {
  minLevel: LogLevel;
}

interface Config {
  database: DatabaseConfig;
  log: LogConfig;
}

let config: Config | undefined;

// Only used for testing
export const resetConfig = () => {
  config = undefined;
};

export const registerConfig = () => {
  const dbHost = process.env.DB_HOST;
  const dbPort = process.env.DB_PORT
    ? parseInt(process.env.DB_PORT)
    : undefined;
  const dbDatabase = process.env.DB_DATABASE;
  const dbUser = process.env.DB_USER;
  const dbPass = process.env.DB_PASS;

  const logMinLevel = process.env.LOG_MIN_LEVEL;

  if (
    !dbHost ||
    !dbPort ||
    Number.isNaN(dbPort) ||
    !dbDatabase ||
    !dbUser ||
    !dbPass ||
    !logMinLevel
  ) {
    console.error(
      dbHost,
      dbPort,
      dbPass,
      dbDatabase,
      dbUser,
      dbPass,
      logMinLevel,
    );
    throw new Error("Malformed config");
  }

  if (!(logMinLevel in LogLevel)) {
    throw new Error("Malformed config");
  }

  config = {
    database: {
      host: dbHost,
      port: dbPort,
      database: dbDatabase,
      username: dbUser,
      password: dbPass,
    },
    log: {
      minLevel: LogLevel[logMinLevel as keyof typeof LogLevel],
    },
  };
};

export const getConfig = () => {
  if (config === undefined) {
    registerConfig();
  }

  return config as Config;
};
