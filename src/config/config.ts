import { application } from "express";
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

interface ApplicationConfig {
  port: number;
}

interface Config {
  database: DatabaseConfig;
  log: LogConfig;
  application: ApplicationConfig;
}

let config: Config | undefined;

// Only used for testing
export const resetConfig = () => {
  config = undefined;
};

const num = (v: string | undefined) => (v ? parseInt(v) : undefined);

export const registerConfig = () => {
  const dbHost = process.env.DB_HOST;
  const dbPort = num(process.env.DB_PORT);
  const dbDatabase = process.env.DB_DATABASE;
  const dbUser = process.env.DB_USER;
  const dbPass = process.env.DB_PASS;

  const logMinLevel = process.env.LOG_MIN_LEVEL;

  const applicationPort = num(process.env.APPLICATION_PORT);

  if (
    !dbHost ||
    !dbPort ||
    Number.isNaN(dbPort) ||
    !dbDatabase ||
    !dbUser ||
    !dbPass ||
    !logMinLevel ||
    !applicationPort
  ) {
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
    application: {
      port: applicationPort,
    },
  };
};

export const getConfig = () => {
  if (config === undefined) {
    registerConfig();
  }

  return config as Config;
};
