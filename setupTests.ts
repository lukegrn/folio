import { mock } from "bun:test";

mock.module("../../logger", () => {
  return {
    logInfoForRequest: mock(),
    logErrorForRequest: mock(),
    log: {
      error: mock(),
      info: mock(),
    },
  };
});
